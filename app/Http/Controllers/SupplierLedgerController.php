<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Supplier;
use App\Models\SupplierBill;
use App\Models\SupplierPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SupplierLedgerController extends Controller
{
    public function show(Supplier $supplier)
    {
        return view('suppliers.ledger', [
            'supplier' => $supplier,
            's' => Setting::getAll(),
            'dataUrl' => route('supplier.ledger.data', $supplier),
        ]);
    }

    public function data(Supplier $supplier, Request $request)
    {
        $ledger = $this->buildLedger($supplier, $request->query('from'), $request->query('to'));

        $ledger['pdf_url'] = route('supplier.ledger.pdf', [
            'supplier' => $supplier,
            'from' => $request->query('from'),
            'to' => $request->query('to'),
        ]);

        return response()->json($ledger);
    }

    public function pdf(Supplier $supplier, Request $request)
    {
        $ledger = $this->buildLedger($supplier, $request->query('from'), $request->query('to'));

        $pdf = Pdf::loadView('suppliers.ledger_pdf', [
            'ledger' => $ledger,
            'supplier' => $supplier,
            's' => Setting::getAll(),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
        ])->setPaper('a4');

        return $pdf->download('supplier-ledger-' . $supplier->id . '.pdf');
    }

    public function storeBill(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'bill_no' => ['nullable', 'string', 'max:100'],
            'bill_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        SupplierBill::create($data);

        return response()->json(['ok' => true]);
    }

    public function storePayment(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'method' => ['required', 'in:cash,upi,bank,cheque,other'],
            'reference' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        SupplierPayment::create($data);

        return response()->json(['ok' => true]);
    }

    private function buildLedger(Supplier $supplier, ?string $from, ?string $to): array
    {
        $opening = 0.0;
        if ($from) {
            $opening = (float) SupplierBill::where('supplier_id', $supplier->id)->whereDate('bill_date', '<', $from)->sum('amount')
                - (float) SupplierPayment::where('supplier_id', $supplier->id)->whereDate('payment_date', '<', $from)->sum('amount');
        }

        $bills = SupplierBill::where('supplier_id', $supplier->id)
            ->when($from, fn ($q) => $q->whereDate('bill_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('bill_date', '<=', $to))
            ->get()
            ->map(fn (SupplierBill $b) => [
                'date' => $b->bill_date->format('Y-m-d'),
                'sort' => $b->bill_date->format('Y-m-d') . '-1-' . str_pad($b->id, 10, '0', STR_PAD_LEFT),
                'type' => 'bill',
                'label' => 'Bill' . ($b->bill_no ? ' ' . $b->bill_no : ''),
                'detail' => $b->note ?: '',
                'debit' => 0.0,
                'credit' => (float) $b->amount,
                'url' => null,
            ]);

        $payments = SupplierPayment::where('supplier_id', $supplier->id)
            ->when($from, fn ($q) => $q->whereDate('payment_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('payment_date', '<=', $to))
            ->get()
            ->map(fn (SupplierPayment $p) => [
                'date' => $p->payment_date->format('Y-m-d'),
                'sort' => $p->payment_date->format('Y-m-d') . '-2-' . str_pad($p->id, 10, '0', STR_PAD_LEFT),
                'type' => 'payment',
                'label' => 'Payment (' . strtoupper($p->method) . ')',
                'detail' => trim(($p->reference ? $p->reference . ' ' : '') . ($p->note ?: '')),
                'debit' => (float) $p->amount,
                'credit' => 0.0,
                'url' => null,
            ]);

        $entries = $bills->concat($payments)->sortBy('sort')->values();

        $balance = $opening;
        $entries = $entries->map(function ($e) use (&$balance) {
            $balance += $e['credit'] - $e['debit'];
            $e['balance'] = round($balance, 2);
            return $e;
        });

        return [
            'supplier' => [
                'id' => $supplier->id,
                'firm_name' => $supplier->firm_name,
                'mobile' => $supplier->mobile,
            ],
            'opening' => round($opening, 2),
            'entries' => $entries,
            'total_debit' => round($entries->sum('debit'), 2),
            'total_credit' => round($entries->sum('credit'), 2),
            'closing' => round($balance, 2),
        ];
    }
}
