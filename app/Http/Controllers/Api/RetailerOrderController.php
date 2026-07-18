<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class RetailerOrderController extends Controller
{
    public function store(Request $request)
    {
        $partner = $request->user();

        $data = $request->validate([
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'exists:products,id'],
            'lines.*.qty' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $order = DB::transaction(function () use ($data, $partner) {
            $productIds = collect($data['lines'])->pluck('product_id')->unique();
            $products = Product::with(['brand', 'category'])
                ->where('is_active', true)
                ->where('is_visible', true)
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            $order = Order::create([
                'partner_id' => $partner->id,
                'status' => 'pending',
                'note' => $data['note'] ?? null,
            ]);

            foreach ($data['lines'] as $l) {
                $product = $products[$l['product_id']] ?? null;
                if (!$product) {
                    continue; // silently skip items no longer offered
                }
                $order->lines()->create([
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'brand' => $product->brand->name,
                    'category' => $product->category->name,
                    'mrp' => $product->mrp,
                    'qty' => (int) $l['qty'],
                ]);
            }

            if ($order->lines()->count() === 0) {
                abort(422, 'No valid items in the order.');
            }

            return $order;
        });

        return response()->json(['ok' => true, 'id' => $order->id]);
    }

    public function index(Request $request)
    {
        $orders = Order::where('partner_id', $request->user()->id)
            ->withCount('lines')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return response()->json([
            'ok' => true,
            'items' => $orders->map(fn (Order $o) => $this->summary($o)),
        ]);
    }

    public function show(Request $request, Order $order)
    {
        $this->authorizeOrder($request, $order);
        $order->loadCount('lines');
        $order->load(['lines' => fn ($q) => $q->orderBy('brand')->orderBy('category')->orderBy('name')]);

        $out = $this->summary($order);
        $out['note'] = $order->note;
        $out['lines'] = $order->lines->map(fn ($l) => [
            'name' => $l->name,
            'brand' => $l->brand,
            'category' => $l->category,
            'mrp' => (float) $l->mrp,
            'qty' => $l->qty,
        ])->values();

        return response()->json(['ok' => true, 'order' => $out]);
    }

    public function cancel(Request $request, Order $order)
    {
        $this->authorizeOrder($request, $order);

        if ($order->status !== 'pending') {
            return response()->json(['ok' => false, 'message' => 'Only pending orders can be cancelled.'], 422);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json(['ok' => true]);
    }

    private function summary(Order $order): array
    {
        $row = [
            'id' => $order->id,
            'status' => $order->status,
            'placed_at' => $order->created_at->format('d M Y, h:i A'),
            'line_count' => $order->lines_count ?? $order->lines()->count(),
        ];

        if ($order->status === 'invoiced' && $order->invoice_id) {
            $row['invoice_url'] = URL::signedRoute('invoices.public', ['invoice' => $order->invoice_id]);
        }

        return $row;
    }

    private function authorizeOrder(Request $request, Order $order): void
    {
        abort_unless($order->partner_id === $request->user()->id, 404);
    }
}
