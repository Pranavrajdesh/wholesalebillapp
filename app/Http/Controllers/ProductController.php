<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\RateGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index', [
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function data(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $brandId = $request->query('brand_id');
        $categoryId = $request->query('category_id');
        $sort = $request->query('sort', 'name_asc');
        $status = $request->query('status', 'active');
        $offset = max(0, (int) $request->query('offset', 0));
        $limit = min(50, max(1, (int) $request->query('limit', 25)));
        $withSlabs = $request->boolean('with_slabs');
        $groupId = $withSlabs ? RateGroup::firstOrCreate(['name' => 'General'])->id : null;

        $query = Product::with(['brand', 'category'])
            ->when($withSlabs, fn ($x) => $x->with(['rateSlabs' => fn ($s) => $s->where('rate_group_id', $groupId)->orderBy('min_qty')]))
            ->when($status !== 'all', fn ($x) => $x->where('is_active', true))
            ->when($q !== '', function ($x) use ($q) {
                $x->where(function ($w) use ($q) {
                    $w->where('name', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%')
                      ->orWhere('barcode', $q);
                });
            })
            ->when($brandId, fn ($x) => $x->where('brand_id', $brandId))
            ->when($categoryId, fn ($x) => $x->where('category_id', $categoryId));

        $total = (clone $query)->count();

        $query = match ($sort) {
            'name_desc' => $query->orderBy('name', 'desc'),
            'mrp_asc'   => $query->orderBy('mrp')->orderBy('name'),
            'mrp_desc'  => $query->orderBy('mrp', 'desc')->orderBy('name'),
            default     => $query->orderBy('name'),
        };

        $items = $query->skip($offset)->take($limit + 1)->get();
        $hasMore = $items->count() > $limit;
        $items = $items->take($limit);

        return response()->json([
            'items' => $items->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'brand' => $p->brand->name,
                'category' => $p->category->name,
                'mrp' => (float) $p->mrp,
                'tax_percent' => (float) $p->tax_percent,
                'tax_inclusive' => (bool) $p->tax_inclusive,
                'barcode' => $p->barcode,
                'image_url' => $p->image_path ? asset('storage/' . $p->image_path) : null,
                'initials' => $p->initials(),
                'track_stock' => $p->track_stock,
                'stock_qty' => $p->stock_qty,
                'is_active' => $p->is_active,
                'edit_url' => route('products.edit', $p),
                'slabs_url' => route('products.slabs', $p),
                'slabs' => $withSlabs ? $p->rateSlabs->map(fn ($s) => [
                    'min_qty' => $s->min_qty,
                    'rate' => (float) $s->rate,
                    'scheme_percent' => (float) $s->scheme_percent,
                    'offer_buy_qty' => $s->offer_buy_qty,
                    'offer_free_qty' => $s->offer_free_qty,
                ])->values() : null,
            ])->values(),
            'total' => $total,
            'has_more' => $hasMore,
            'next_offset' => $offset + $items->count(),
        ]);
    }

    public function create()
    {
        return view('products.create', [
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand_id' => ['required', 'exists:brands,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode'],
            'mrp' => ['required', 'numeric', 'min:0'],
            'hsn_code' => ['nullable', 'string', 'max:20'],
            'tax_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'tax_inclusive' => ['required', 'boolean'],
            'track_stock' => ['required', 'boolean'],
            'is_visible' => ['required', 'boolean'],
            'rate_visible' => ['required', 'boolean'],
            'stock_qty' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        unset($data['image']);
        $data['stock_qty'] = $data['stock_qty'] ?? 0;

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('status', 'Product created: ' . $data['name']);
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'product' => $product,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand_id' => ['required', 'exists:brands,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'barcode' => ['nullable', 'string', 'max:50', Rule::unique('products', 'barcode')->ignore($product->id)],
            'mrp' => ['required', 'numeric', 'min:0'],
            'hsn_code' => ['nullable', 'string', 'max:20'],
            'tax_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'tax_inclusive' => ['required', 'boolean'],
            'track_stock' => ['required', 'boolean'],
            'is_visible' => ['required', 'boolean'],
            'rate_visible' => ['required', 'boolean'],
            'stock_qty' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        unset($data['image']);
        $data['stock_qty'] = $data['stock_qty'] ?? 0;

        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('status', 'Product updated: ' . $product->name);
    }

    public function importForm()
    {
        return view('products.import');
    }

    public function sampleCsv()
    {
        $rows = [
            ['name', 'brand', 'category', 'barcode', 'mrp', 'hsn', 'tax_percent', 'tax_inclusive', 'stock'],
            ['Lifebuoy Total Soap 125g', 'Lifebuoy', 'Soap', '8901030510014', '36', '3401', '18', '1', '100'],
            ['Clinic Plus Strong Shampoo 175ml', 'Clinic Plus', 'Shampoo', '8901030510021', '115', '3305', '18', '1', '0'],
            ['Maggi Noodles 70g', 'Nestle', 'Snacks', '', '14', '1902', '12', '1', '250'],
        ];

        $out = fopen('php://temp', 'r+');
        foreach ($rows as $r) {
            fputcsv($out, $r);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products-sample.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (!$header) {
            fclose($handle);
            return back()->withErrors(['file' => 'The file is empty.']);
        }

        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        $header = array_map(fn ($h) => strtolower(trim((string) $h)), $header);

        foreach (['name', 'brand', 'category', 'mrp'] as $col) {
            if (!in_array($col, $header, true)) {
                fclose($handle);
                return back()->withErrors(['file' => "Missing required column: {$col}"]);
            }
        }

        $created = 0;
        $updated = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            if ($rowNum > 2001) {
                $errors[] = 'Stopped: file exceeds the 2000 row limit.';
                break;
            }

            if (count($row) === 1 && trim((string) $row[0]) === '') {
                continue;
            }

            $d = [];
            foreach ($header as $i => $col) {
                $d[$col] = trim((string) ($row[$i] ?? ''));
            }

            if ($d['name'] === '' || $d['brand'] === '' || $d['category'] === '') {
                $errors[] = "Row {$rowNum}: name, brand and category are required.";
                continue;
            }

            if (!is_numeric($d['mrp'])) {
                $errors[] = "Row {$rowNum}: MRP must be a number.";
                continue;
            }

            $tax = ($d['tax_percent'] ?? '') !== '' ? (float) $d['tax_percent'] : 0.0;
            if (!is_numeric($d['tax_percent'] ?? '0') || $tax < 0 || $tax > 100) {
                $errors[] = "Row {$rowNum}: tax_percent must be between 0 and 100.";
                continue;
            }

            $stock = ($d['stock'] ?? '') !== '' ? max(0, (int) $d['stock']) : 0;
            $inclusive = !in_array(strtolower($d['tax_inclusive'] ?? '1'), ['0', 'no', 'false'], true);
            $barcode = ($d['barcode'] ?? '') !== '' ? $d['barcode'] : null;

            $attrs = [
                'name' => $d['name'],
                'brand_id' => Brand::firstOrCreate(['name' => $d['brand']])->id,
                'category_id' => Category::firstOrCreate(['name' => $d['category']])->id,
                'mrp' => (float) $d['mrp'],
                'hsn_code' => ($d['hsn'] ?? '') !== '' ? $d['hsn'] : null,
                'tax_percent' => $tax,
                'tax_inclusive' => $inclusive,
                'track_stock' => $stock > 0,
                'stock_qty' => $stock,
            ];

            $existing = $barcode
                ? Product::where('barcode', $barcode)->first()
                : Product::where('name', $d['name'])->first();

            if ($existing) {
                $existing->update($attrs);
                $updated++;
            } else {
                Product::create($attrs + ['barcode' => $barcode, 'is_active' => true]);
                $created++;
            }
        }

        fclose($handle);

        return redirect()
            ->route('products.import.form')
            ->with('status', "Import complete: {$created} created, {$updated} updated"
                . ($errors ? ', ' . count($errors) . ' problem row(s) below' : ''))
            ->with('import_errors', $errors);
    }
}
