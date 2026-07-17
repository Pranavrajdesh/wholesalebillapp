<?php

namespace App\Http\Controllers;

use App\Models\InwardEntry;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InwardController extends Controller
{
    public function index()
    {
        return view('inward.index');
    }

    public function data(Request $request)
    {
        $offset = max(0, (int) $request->query('offset', 0));
        $limit = min(50, max(1, (int) $request->query('limit', 25)));

        $query = InwardEntry::with('supplier')->withCount('lines')->orderByDesc('id');

        $total = (clone $query)->count();
        $entries = $query->skip($offset)->take($limit)->get();

        return response()->json([
            'items' => $entries->map(fn (InwardEntry $e) => [
                'id' => $e->id,
                'date' => $e->inward_date->format('d M Y'),
                'supplier' => $e->supplier?->firm_name,
                'note' => $e->note,
                'line_count' => $e->lines_count,
                'url' => route('inward.show', $e),
            ])->values(),
            'total' => $total,
            'next_offset' => $offset + $entries->count(),
            'has_more' => $offset + $entries->count() < $total,
        ]);
    }

    public function create()
    {
        return view('inward.create', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('firm_name')->get(['id', 'firm_name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'inward_date' => ['required', 'date'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'note' => ['nullable', 'string', 'max:255'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'exists:products,id'],
            'lines.*.qty' => ['required', 'integer', 'min:1'],
            'lines.*.purchase_rate' => ['nullable', 'numeric', 'gt:0'],
        ]);

        $entry = DB::transaction(function () use ($data) {
            $productIds = collect($data['lines'])->pluck('product_id')->unique();
            $products = Product::with(['brand', 'category'])
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $entry = InwardEntry::create([
                'inward_date' => $data['inward_date'],
                'supplier_id' => $data['supplier_id'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            $rows = [];
            foreach ($data['lines'] as $l) {
                $product = $products[$l['product_id']];
                $qty = (int) $l['qty'];

                $rows[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'brand' => $product->brand->name,
                    'category' => $product->category->name,
                    'qty' => $qty,
                    'purchase_rate' => isset($l['purchase_rate']) && $l['purchase_rate'] !== null && $l['purchase_rate'] !== ''
                        ? round((float) $l['purchase_rate'], 2)
                        : null,
                ];

                $product->increment('stock_qty', $qty);
            }

            $entry->lines()->createMany($rows);

            return $entry;
        });

        return response()->json([
            'ok' => true,
            'id' => $entry->id,
            'url' => route('inward.show', $entry),
        ]);
    }

    public function show(InwardEntry $inward)
    {
        $inward->load(['supplier', 'lines' => fn ($q) => $q->orderBy('brand')->orderBy('category')->orderBy('name'), 'lines.product']);

        return view('inward.show', ['entry' => $inward]);
    }
}
