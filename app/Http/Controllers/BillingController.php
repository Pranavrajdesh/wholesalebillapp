<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\HeldBill;
use App\Models\Partner;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function selectPartner(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $partners = Partner::where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('firm_name', 'like', "%{$q}%")
                      ->orWhere('contact_name', 'like', "%{$q}%")
                      ->orWhere('mobile', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('updated_at')
            ->simplePaginate(25)
            ->withQueryString();

        return view('billing.select', compact('partners', 'q'));
    }

    public function cart()
    {
        return view('billing.cart');
    }

    public function checkout()
    {
        return view('billing.checkout');
    }

    public function catalogue()
    {
        return view('billing.catalogue', [
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function holdStore(Request $request)
    {
        $data = $request->validate([
            'partner_id' => ['required', 'exists:partners,id'],
            'payload' => ['required', 'array'],
            'payload.lines' => ['required', 'array', 'min:1'],
        ]);

        $held = HeldBill::create([
            'partner_id' => $data['partner_id'],
            'payload' => $data['payload'],
        ]);

        return response()->json(['ok' => true, 'id' => $held->id]);
    }

    public function holdIndex()
    {
        $held = HeldBill::with('partner')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        return response()->json([
            'items' => $held->map(function (HeldBill $h) {
                $lines = $h->payload['lines'] ?? [];
                $total = collect($lines)->sum(fn ($l) => ($l['qty'] ?? 0) * ($l['rate'] ?? 0));

                return [
                    'id' => $h->id,
                    'firm_name' => $h->partner->firm_name,
                    'held_at' => $h->created_at->format('d M Y, h:i A'),
                    'line_count' => count($lines),
                    'total' => round($total, 2),
                ];
            })->values(),
        ]);
    }

    public function holdShow(HeldBill $heldBill)
    {
        return response()->json([
            'id' => $heldBill->id,
            'partner' => [
                'id' => $heldBill->partner->id,
                'firm_name' => $heldBill->partner->firm_name,
                'mobile' => $heldBill->partner->mobile,
            ],
            'payload' => $heldBill->payload,
        ]);
    }

    public function holdDestroy(HeldBill $heldBill)
    {
        $heldBill->delete();

        return response()->json(['ok' => true]);
    }
}
