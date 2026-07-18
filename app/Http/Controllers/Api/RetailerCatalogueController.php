<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\RateGroup;
use Illuminate\Http\Request;

class RetailerCatalogueController extends Controller
{
    public function filters()
    {
        return response()->json([
            'ok' => true,
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function products(Request $request)
    {
        $partner = $request->user();

        $q = trim((string) $request->query('q', ''));
        $brandId = $request->query('brand_id');
        $categoryId = $request->query('category_id');
        $offset = max(0, (int) $request->query('offset', 0));
        $limit = min(50, max(1, (int) $request->query('limit', 25)));

        $groupId = RateGroup::firstOrCreate(['name' => 'General'])->id;

        $query = Product::with(['brand', 'category', 'rateSlabs' => fn ($s) => $s->where('rate_group_id', $groupId)->orderBy('min_qty')])
            ->where('is_active', true)
            ->where('is_visible', true)
            ->when($q !== '', function ($x) use ($q) {
                $x->where(function ($w) use ($q) {
                    $w->where('name', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%')
                      ->orWhere('barcode', $q);
                });
            })
            ->when($brandId, fn ($x) => $x->where('brand_id', $brandId))
            ->when($categoryId, fn ($x) => $x->where('category_id', $categoryId))
            ->orderBy('name');

        $total = (clone $query)->count();
        $items = $query->skip($offset)->take($limit)->get();

        return response()->json([
            'ok' => true,
            'show_prices' => (bool) $partner->show_prices,
            'items' => $items->map(function (Product $p) use ($partner) {
                $showRates = $partner->show_prices && $p->rate_visible;

                $row = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'brand' => $p->brand->name,
                    'category' => $p->category->name,
                    'mrp' => (float) $p->mrp,
                    'barcode' => $p->barcode,
                    'image_url' => $p->image_path ? asset('storage/' . $p->image_path) : null,
                    'initials' => $p->initials(),
                    'rates_visible' => $showRates,
                ];

                if ($showRates) {
                    $row['slabs'] = $p->rateSlabs->map(fn ($s) => [
                        'min_qty' => $s->min_qty,
                        'rate' => (float) $s->rate,
                        'scheme_percent' => (float) $s->scheme_percent,
                        'offer_buy_qty' => $s->offer_buy_qty,
                        'offer_free_qty' => $s->offer_free_qty,
                    ])->values();
                }

                return $row;
            })->values(),
            'total' => $total,
            'next_offset' => $offset + $items->count(),
            'has_more' => $offset + $items->count() < $total,
        ]);
    }
}
