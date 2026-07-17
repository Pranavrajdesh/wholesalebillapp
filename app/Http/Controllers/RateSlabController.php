<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RateGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RateSlabController extends Controller
{
    public function edit(Product $product)
    {
        $group = RateGroup::firstOrCreate(['name' => 'General']);

        $slabs = $product->rateSlabs()
            ->where('rate_group_id', $group->id)
            ->orderBy('min_qty')
            ->get();

        return view('products.slabs', compact('product', 'slabs'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'slabs' => ['nullable', 'array', 'max:20'],
            'slabs.*.min_qty' => ['required', 'integer', 'min:1'],
            'slabs.*.rate' => ['required', 'numeric', 'gt:0'],
            'slabs.*.scheme_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'slabs.*.offer_buy_qty' => ['nullable', 'integer', 'min:1'],
            'slabs.*.offer_free_qty' => ['nullable', 'integer', 'min:1'],
        ]);

        $slabs = collect($data['slabs'] ?? [])->values();

        $minQtys = $slabs->pluck('min_qty');
        if ($minQtys->count() !== $minQtys->unique()->count()) {
            return back()
                ->withErrors(['slabs' => 'Two rows have the same minimum quantity. Each slab must start at a different quantity.'])
                ->withInput();
        }

        foreach ($slabs as $i => $slab) {
            $hasBuy = !empty($slab['offer_buy_qty']);
            $hasFree = !empty($slab['offer_free_qty']);
            if ($hasBuy !== $hasFree) {
                return back()
                    ->withErrors(['slabs' => 'Row ' . ($i + 1) . ': a free offer needs both Buy qty and Free qty (e.g. 12 + 2).'])
                    ->withInput();
            }
        }

        $group = RateGroup::firstOrCreate(['name' => 'General']);

        DB::transaction(function () use ($product, $group, $slabs) {
            $product->rateSlabs()->where('rate_group_id', $group->id)->delete();

            foreach ($slabs->sortBy('min_qty') as $slab) {
                $product->rateSlabs()->create([
                    'rate_group_id' => $group->id,
                    'min_qty' => (int) $slab['min_qty'],
                    'rate' => (float) $slab['rate'],
                    'scheme_percent' => (float) ($slab['scheme_percent'] ?? 0),
                    'offer_buy_qty' => $slab['offer_buy_qty'] ?? null,
                    'offer_free_qty' => $slab['offer_free_qty'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('products.slabs', $product)
            ->with('status', 'Rates saved for ' . $product->name . ' (' . $slabs->count() . ' slab' . ($slabs->count() === 1 ? '' : 's') . ')');
    }
}
