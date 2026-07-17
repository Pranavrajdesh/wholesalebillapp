<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\RateGroup;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('orders.index');
    }

    public function data(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = Order::with('partner')
            ->withCount('lines')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('id');

        $orders = $query->limit(100)->get();

        return response()->json([
            'items' => $orders->map(fn (Order $o) => [
                'id' => $o->id,
                'status' => $o->status,
                'firm_name' => $o->partner->firm_name,
                'placed_at' => $o->created_at->format('d M Y, h:i A'),
                'line_count' => $o->lines_count,
                'note' => $o->note,
                'invoice_url' => $o->invoice_id ? route('invoices.show', $o->invoice_id) : null,
            ]),
            'pending_count' => Order::where('status', 'pending')->count(),
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['partner', 'lines' => fn ($q) => $q->orderBy('brand')->orderBy('category')->orderBy('name')]);

        $groupId = RateGroup::firstOrCreate(['name' => 'General'])->id;

        $products = Product::with(['rateSlabs' => fn ($q) => $q->where('rate_group_id', $groupId)->orderBy('min_qty')])
            ->whereIn('id', $order->lines->pluck('product_id'))
            ->get()
            ->keyBy('id');

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'note' => $order->note,
            'placed_at' => $order->created_at->format('d M Y, h:i A'),
            'partner' => [
                'id' => $order->partner->id,
                'firm_name' => $order->partner->firm_name,
                'mobile' => $order->partner->mobile,
            ],
            'invoice_url' => $order->invoice_id ? route('invoices.show', $order->invoice_id) : null,
            'lines' => $order->lines->map(function ($l) use ($products) {
                $p = $products[$l->product_id] ?? null;

                return [
                    'product_id' => $l->product_id,
                    'name' => $l->name,
                    'brand' => $l->brand,
                    'category' => $l->category,
                    'mrp' => (float) $l->mrp,
                    'qty' => $l->qty,
                    'available' => (bool) ($p && $p->is_active),
                    'slabs' => $p ? $p->rateSlabs->map(fn ($s) => [
                        'min_qty' => $s->min_qty,
                        'rate' => (float) $s->rate,
                        'scheme_percent' => (float) $s->scheme_percent,
                        'offer_buy_qty' => $s->offer_buy_qty,
                        'offer_free_qty' => $s->offer_free_qty,
                    ])->values() : [],
                ];
            })->values(),
        ]);
    }

    public function markInvoiced(Request $request, Order $order)
    {
        $data = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
        ]);

        if ($order->status !== 'pending') {
            return response()->json(['ok' => false, 'message' => 'Order is not pending.'], 422);
        }

        $order->update(['status' => 'invoiced', 'invoice_id' => $data['invoice_id']]);

        return response()->json(['ok' => true]);
    }

    public function cancel(Order $order)
    {
        if ($order->status !== 'pending') {
            return response()->json(['ok' => false, 'message' => 'Order is not pending.'], 422);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json(['ok' => true]);
    }
}
