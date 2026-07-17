<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $suppliers = Supplier::query()
            ->when($q !== '', function ($x) use ($q) {
                $x->where(function ($w) use ($q) {
                    $w->where('firm_name', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%')
                      ->orWhere('contact_name', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%')
                      ->orWhere('mobile', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%');
                });
            })
            ->orderBy('firm_name')
            ->get();

        return view('suppliers.index', ['suppliers' => $suppliers, 'q' => $q]);
    }

    public function data(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $offset = max(0, (int) $request->query('offset', 0));
        $limit = min(50, max(1, (int) $request->query('limit', 25)));

        $query = Supplier::query()
            ->when($q !== '', function ($x) use ($q) {
                $x->where(function ($w) use ($q) {
                    $w->where('firm_name', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%')
                      ->orWhere('contact_name', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%')
                      ->orWhere('mobile', 'like', '%' . preg_replace('/[\s\-]+/', '%', $q) . '%');
                });
            })
            ->orderBy('firm_name');

        $total = (clone $query)->count();
        $suppliers = $query->skip($offset)->take($limit)->get();

        return response()->json([
            'items' => $suppliers->map(function (Supplier $s) {
                $initials = collect(explode(' ', $s->firm_name))
                    ->filter()->take(2)->map(fn ($x) => mb_substr($x, 0, 1))->implode('');

                return [
                    'id' => $s->id,
                    'firm_name' => $s->firm_name,
                    'contact_name' => $s->contact_name,
                    'mobile' => $s->mobile,
                    'gst_number' => $s->gst_number,
                    'address' => $s->address,
                    'is_active' => $s->is_active,
                    'initials' => mb_strtoupper($initials),
                    'edit_url' => route('suppliers.edit', $s),
                    'ledger_url' => route('supplier.ledger.show', $s),
                    'ledger_url' => route('supplier.ledger.show', $s),
                ];
            })->values(),
            'total' => $total,
            'next_offset' => $offset + $suppliers->count(),
            'has_more' => $offset + $suppliers->count() < $total,
        ]);
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'firm_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'mobile' => ['required', 'digits:10', 'unique:suppliers,mobile'],
            'gst_number' => ['nullable', 'string', 'max:15'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Supplier::create($data + ['is_active' => true]);

        return redirect()->route('suppliers.index')->with('status', 'Supplier added.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', ['supplier' => $supplier]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'firm_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'mobile' => ['required', 'digits:10', Rule::unique('suppliers', 'mobile')->ignore($supplier->id)],
            'gst_number' => ['nullable', 'string', 'max:15'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ]);

        $supplier->update($data);

        return redirect()->route('suppliers.index')->with('status', 'Supplier updated.');
    }
}
