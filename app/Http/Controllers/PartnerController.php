<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\RateGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $partners = Partner::with('rateGroup')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('firm_name', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%')
                      ->orWhere('contact_name', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%')
                      ->orWhere('mobile', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%');
                });
            })
            ->orderBy('firm_name')
            ->simplePaginate(25)
            ->withQueryString();

        return view('partners.index', compact('partners', 'q'));
    }

    public function data(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'active');
        $sort = $request->query('sort', 'name');
        $offset = max(0, (int) $request->query('offset', 0));
        $limit = min(50, max(1, (int) $request->query('limit', 25)));

        $query = Partner::query()
            ->withSum('invoices as bills_sum', 'total')
            ->withSum('payments as paid_sum', 'amount')
            ->when($status !== 'all', fn ($x) => $x->where('is_active', true))
            ->when($q !== '', function ($x) use ($q) {
                $x->where(function ($w) use ($q) {
                    $w->where('firm_name', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%')
                      ->orWhere('contact_name', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%')
                      ->orWhere('mobile', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%');
                });
            });

        $total = (clone $query)->count();

        $query = $sort === 'recent'
            ? $query->orderByDesc('updated_at')
            : $query->orderBy('firm_name');

        $items = $query->skip($offset)->take($limit + 1)->get();
        $hasMore = $items->count() > $limit;
        $items = $items->take($limit);

        return response()->json([
            'items' => $items->map(fn (Partner $p) => [
                'id' => $p->id,
                'firm_name' => $p->firm_name,
                'contact_name' => $p->contact_name,
                'mobile' => $p->mobile,
                'initials' => $p->initials(),
                'portal_access' => $p->portal_access,
                'is_active' => $p->is_active,
                'edit_url' => route('partners.edit', $p),
                'ledger_url' => route('ledger.show', $p),
                'balance' => round((float) $p->bills_sum - (float) $p->paid_sum, 2),
            ])->values(),
            'total' => $total,
            'has_more' => $hasMore,
            'next_offset' => $offset + $items->count(),
        ]);
    }

    public function create()
    {
        return view('partners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        Partner::create($data);

        return redirect()
            ->route('partners.index')
            ->with('status', 'Partner registered: ' . $data['firm_name']);
    }

    public function edit(Partner $partner)
    {
        return view('partners.edit', [
            'partner' => $partner,
            'rateGroups' => RateGroup::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Partner $partner)
    {
        $data = $request->validate($this->rules($partner));

        $partner->update($data);

        return redirect()
            ->route('partners.index')
            ->with('status', 'Partner updated: ' . $partner->firm_name);
    }

    private function rules(?Partner $ignore = null): array
    {
        return [
            'firm_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'mobile' => [
                'required',
                'digits:10',
                $ignore
                    ? Rule::unique('partners', 'mobile')->ignore($ignore->id)
                    : Rule::unique('partners', 'mobile'),
                function ($attribute, $value, $fail) {
                    if (User::where('mobile', $value)->exists()) {
                        $fail('This mobile number belongs to the owner account.');
                    }
                },
            ],
            'gst_number' => ['nullable', 'string', 'max:15'],
            'alt_mobile' => ['nullable', 'digits:10'],
            'address' => ['nullable', 'string', 'max:500'],
            'portal_access' => ['required', 'boolean'],
            'show_prices' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
