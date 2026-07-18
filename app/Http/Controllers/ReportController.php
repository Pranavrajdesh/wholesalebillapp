<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Product;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // ---------------------------------------------------------------
    // Sales Register
    // ---------------------------------------------------------------
    public function salesRegister(Request $request)
    {
        [$from, $to, $fyActive] = $this->period($request);
        $partnerId = $request->query('partner_id');

        $q = DB::table('invoices')
            ->join('partners', 'partners.id', '=', 'invoices.partner_id')
            ->select('invoices.id', 'invoices.invoice_no', 'invoices.invoice_date', 'partners.firm_name',
                'invoices.subtotal', 'invoices.discount_amount', 'invoices.round_off', 'invoices.total')
            ->orderBy('invoices.invoice_date')->orderBy('invoices.invoice_no');
        if ($from) $q->where('invoice_date', '>=', $from);
        if ($to) $q->where('invoice_date', '<=', $to);
        if ($partnerId) $q->where('invoices.partner_id', $partnerId);
        $rows = $q->get();

        $totals = [
            'count' => $rows->count(),
            'subtotal' => $rows->sum('subtotal'),
            'discount' => $rows->sum('discount_amount'),
            'total' => $rows->sum('total'),
        ];

        if ($request->query('format') === 'csv') {
            return $this->csv('sales-register', ['Date', 'Invoice No', 'Partner', 'Subtotal', 'Discount', 'Round Off', 'Total'],
                $rows->map(fn ($r) => [$r->invoice_date, 'INV-' . $r->invoice_no, $r->firm_name, $r->subtotal, $r->discount_amount, $r->round_off, $r->total]));
        }

        if ($request->query('format') === 'pdf') {
            return $this->pdfOut('Sales Register', $this->periodLabel($from, $to),
                [['Date', 'l'], ['Invoice', 'l'], ['Partner', 'l'], ['Subtotal', 'r'], ['Discount', 'r'], ['Total', 'r']],
                $rows->map(fn ($r) => [
                    Carbon::parse($r->invoice_date)->format('d/m/y'), 'INV-' . $r->invoice_no, $r->firm_name,
                    inr($r->subtotal, 2), $r->discount_amount > 0 ? inr($r->discount_amount, 2) : '',
                    inr($r->total, 2),
                ])->all(),
                ['Total (' . $totals['count'] . ')', '', '', inr($totals['subtotal'], 2), inr($totals['discount'], 2), inr($totals['total'], 2)]);
        }

        return view('reports.sales_register', [
            'rows' => $rows, 'totals' => $totals, 'from' => $from, 'to' => $to, 'fyActive' => $fyActive,
            'partnerId' => $partnerId,
            'partners' => Partner::orderBy('firm_name')->get(['id', 'firm_name']),
        ]);
    }

    // ---------------------------------------------------------------
    // Sales Summary
    // ---------------------------------------------------------------
    public function salesSummary(Request $request)
    {
        [$from, $to, $fyActive] = $this->period($request);
        $group = in_array($request->query('group'), ['month', 'partner', 'brand', 'product'], true)
            ? $request->query('group') : 'month';

        if ($group === 'month' || $group === 'partner') {
            $q = DB::table('invoices')->join('partners', 'partners.id', '=', 'invoices.partner_id');
            if ($from) $q->where('invoice_date', '>=', $from);
            if ($to) $q->where('invoice_date', '<=', $to);
            if ($group === 'month') {
                $rows = $q->select(DB::raw("DATE_FORMAT(invoice_date, '%Y-%m') AS g"),
                        DB::raw('COUNT(*) AS bills'), DB::raw('SUM(total) AS amount'))
                    ->groupBy('g')->orderBy('g')->get();
            } else {
                $rows = $q->select('partners.firm_name AS g',
                        DB::raw('COUNT(*) AS bills'), DB::raw('SUM(invoices.total) AS amount'))
                    ->groupBy('partners.firm_name')->orderByDesc(DB::raw('SUM(invoices.total)'))->get();
            }
            $rows = $rows->map(fn ($r) => (object) ['g' => $r->g, 'bills' => $r->bills, 'qty' => null, 'free' => null, 'amount' => $r->amount]);
        } else {
            $q = DB::table('invoice_lines')->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id');
            if ($from) $q->where('invoices.invoice_date', '>=', $from);
            if ($to) $q->where('invoices.invoice_date', '<=', $to);
            $col = $group === 'brand' ? 'invoice_lines.brand' : 'invoice_lines.name';
            $rows = $q->select(DB::raw("$col AS g"),
                    DB::raw('SUM(invoice_lines.qty) AS qty'), DB::raw('SUM(invoice_lines.free_qty) AS free'),
                    DB::raw('SUM(invoice_lines.amount) AS amount'))
                ->groupBy('g')->orderByDesc(DB::raw('SUM(invoice_lines.amount)'))->get();
            $rows = $rows->map(fn ($r) => (object) ['g' => $r->g, 'bills' => null, 'qty' => $r->qty, 'free' => $r->free, 'amount' => $r->amount]);
        }

        $isLine = in_array($group, ['brand', 'product'], true);

        if ($request->query('format') === 'csv') {
            $head = $isLine ? [ucfirst($group), 'Qty', 'Free', 'Gross Amount'] : [ucfirst($group), 'Bills', 'Amount'];
            return $this->csv('sales-summary-' . $group, $head,
                $rows->map(fn ($r) => $isLine ? [$r->g, $r->qty, $r->free, $r->amount] : [$r->g, $r->bills, $r->amount]));
        }

        if ($request->query('format') === 'pdf') {
            $cols = $isLine
                ? [[ucfirst($group), 'l'], ['Qty', 'r'], ['Free', 'r'], ['Gross Amount', 'r']]
                : [[ucfirst($group), 'l'], ['Bills', 'r'], ['Amount', 'r']];
            $body = $rows->map(fn ($r) => $isLine
                ? [$r->g, (string) $r->qty, $r->free > 0 ? (string) $r->free : '', inr($r->amount, 2)]
                : [$r->g, (string) $r->bills, inr($r->amount, 2)])->all();
            $tot = $isLine
                ? ['Total', '', '', inr($rows->sum('amount'), 2)]
                : ['Total', '', inr($rows->sum('amount'), 2)];
            return $this->pdfOut('Sales Summary — by ' . $group, $this->periodLabel($from, $to), $cols, $body, $tot,
                $isLine ? 'Amounts are gross line values (before invoice-level discount and round-off).' : null);
        }

        return view('reports.sales_summary', [
            'rows' => $rows, 'group' => $group, 'from' => $from, 'to' => $to, 'fyActive' => $fyActive,
            'totalAmount' => $rows->sum('amount'),
        ]);
    }

    // ---------------------------------------------------------------
    // Collections Register
    // ---------------------------------------------------------------
    public function collections(Request $request)
    {
        [$from, $to, $fyActive] = $this->period($request);
        $partnerId = $request->query('partner_id');

        $q = DB::table('payments')
            ->join('partners', 'partners.id', '=', 'payments.partner_id')
            ->select('payments.id', 'payments.payment_date', 'partners.firm_name',
                'payments.amount', 'payments.method', 'payments.reference', 'payments.note')
            ->orderBy('payments.payment_date')->orderBy('payments.id');
        if ($from) $q->where('payment_date', '>=', $from);
        if ($to) $q->where('payment_date', '<=', $to);
        if ($partnerId) $q->where('payments.partner_id', $partnerId);
        $rows = $q->get();

        $byMethod = $rows->groupBy('method')->map(fn ($g) => $g->sum('amount'))->sortDesc();
        $total = $rows->sum('amount');

        if ($request->query('format') === 'csv') {
            return $this->csv('collections', ['Date', 'Partner', 'Method', 'Reference', 'Amount', 'Note'],
                $rows->map(fn ($r) => [$r->payment_date, $r->firm_name, strtoupper($r->method), $r->reference, $r->amount, $r->note]));
        }

        if ($request->query('format') === 'pdf') {
            $note = $byMethod->map(fn ($amt, $m) => strtoupper($m) . ': Rs ' . inr($amt, 2))->implode(' · ');
            return $this->pdfOut('Collections Register', $this->periodLabel($from, $to),
                [['Date', 'l'], ['Partner', 'l'], ['Method', 'l'], ['Reference', 'l'], ['Amount', 'r']],
                $rows->map(fn ($r) => [
                    Carbon::parse($r->payment_date)->format('d/m/y'), $r->firm_name, strtoupper($r->method),
                    trim(($r->reference ?? '') . ($r->note ? ' · ' . $r->note : '')), inr($r->amount, 2),
                ])->all(),
                ['Total (' . $rows->count() . ')', '', '', '', inr($total, 2)],
                $note ?: null);
        }

        return view('reports.collections', [
            'rows' => $rows, 'byMethod' => $byMethod, 'total' => $total,
            'from' => $from, 'to' => $to, 'partnerId' => $partnerId, 'fyActive' => $fyActive,
            'partners' => Partner::orderBy('firm_name')->get(['id', 'firm_name']),
        ]);
    }

    // ---------------------------------------------------------------
    // Outstanding & Aging
    // ---------------------------------------------------------------
    public function outstanding(Request $request)
    {
        $today = Carbon::today();

        $invoices = DB::table('invoices')
            ->select('partner_id', 'invoice_date', 'total')
            ->orderBy('partner_id')->orderBy('invoice_date')->orderBy('invoice_no')
            ->get()->groupBy('partner_id');
        $credits = DB::table('payments')->select('partner_id', DB::raw('SUM(amount) AS t'))
            ->groupBy('partner_id')->pluck('t', 'partner_id');
        $cns = DB::table('credit_notes')->select('partner_id', DB::raw('SUM(total) AS t'))
            ->groupBy('partner_id')->pluck('t', 'partner_id');

        $partners = Partner::orderBy('firm_name')->get(['id', 'firm_name', 'mobile']);

        $rows = [];
        $grand = ['b0' => 0.0, 'b31' => 0.0, 'b61' => 0.0, 'b91' => 0.0, 'total' => 0.0, 'advance' => 0.0];

        foreach ($partners as $p) {
            $credit = (float) ($credits[$p->id] ?? 0) + (float) ($cns[$p->id] ?? 0);
            $buckets = ['b0' => 0.0, 'b31' => 0.0, 'b61' => 0.0, 'b91' => 0.0];
            $due = 0.0;

            foreach ($invoices[$p->id] ?? [] as $inv) {
                $amount = (float) $inv->total;
                $settled = min($credit, $amount);
                $credit -= $settled;
                $open = $amount - $settled;
                if ($open <= 0.009) continue;
                $age = Carbon::parse($inv->invoice_date)->diffInDays($today);
                $key = $age <= 30 ? 'b0' : ($age <= 60 ? 'b31' : ($age <= 90 ? 'b61' : 'b91'));
                $buckets[$key] += $open;
                $due += $open;
            }

            $advance = $credit;
            if ($due > 0.009 || $advance > 0.009) {
                $rows[] = (object) array_merge([
                    'id' => $p->id, 'firm_name' => $p->firm_name, 'due' => $due, 'advance' => $advance,
                ], $buckets);
                foreach ($buckets as $k => $vv) $grand[$k] += $vv;
                $grand['total'] += $due;
                $grand['advance'] += $advance;
            }
        }

        usort($rows, fn ($a, $b) => $b->due <=> $a->due);

        if ($request->query('format') === 'csv') {
            return $this->csv('outstanding-aging',
                ['Partner', '0-30 days', '31-60 days', '61-90 days', 'Over 90 days', 'Total Due', 'Advance Held'],
                collect($rows)->map(fn ($r) => [$r->firm_name, $r->b0, $r->b31, $r->b61, $r->b91, $r->due, $r->advance]));
        }

        if ($request->query('format') === 'pdf') {
            $fmt = fn ($n) => $n > 0 ? inr($n, 2) : '';
            return $this->pdfOut('Outstanding & Aging', 'As of ' . $today->format('d M Y'),
                [['Partner', 'l'], ['0-30d', 'r'], ['31-60d', 'r'], ['61-90d', 'r'], ['90d+', 'r'], ['Total Due', 'r'], ['Advance', 'r']],
                collect($rows)->map(fn ($r) => [$r->firm_name, $fmt($r->b0), $fmt($r->b31), $fmt($r->b61), $fmt($r->b91), $fmt($r->due), $fmt($r->advance)])->all(),
                ['Total', inr($grand['b0'], 2), inr($grand['b31'], 2), inr($grand['b61'], 2), inr($grand['b91'], 2), inr($grand['total'], 2), inr($grand['advance'], 2)],
                'Payments and credit notes settle the oldest invoices first; what remains is bucketed by invoice age.');
        }

        return view('reports.outstanding', ['rows' => $rows, 'grand' => $grand]);
    }

    // ---------------------------------------------------------------
    // Stock Report
    // ---------------------------------------------------------------
    public function stock(Request $request)
    {
        $filter = in_array($request->query('filter'), ['all', 'low', 'negative', 'out'], true)
            ? $request->query('filter') : 'all';

        $q = Product::with(['brand', 'category'])
            ->where('is_active', true)
            ->orderBy('brand_id')->orderBy('name');
        if ($filter === 'low') $q->whereBetween('stock_qty', [0, 10]);
        if ($filter === 'negative') $q->where('stock_qty', '<', 0);
        if ($filter === 'out') $q->where('stock_qty', '<=', 0);
        $products = $q->get();

        $rows = $products->map(fn ($p) => (object) [
            'name' => $p->name, 'brand' => $p->brand->name,
            'stock' => (int) $p->stock_qty, 'mrp' => (float) $p->mrp,
            'value' => max(0, (int) $p->stock_qty) * (float) $p->mrp,
        ]);

        $totals = [
            'items' => $rows->count(),
            'units' => $rows->sum(fn ($r) => max(0, $r->stock)),
            'value' => $rows->sum('value'),
            'low' => $rows->filter(fn ($r) => $r->stock >= 0 && $r->stock <= 10)->count(),
            'negative' => $rows->filter(fn ($r) => $r->stock < 0)->count(),
        ];

        if ($request->query('format') === 'csv') {
            return $this->csv('stock-report', ['Brand', 'Product', 'Stock Qty', 'MRP', 'Value at MRP'],
                $rows->map(fn ($r) => [$r->brand, $r->name, $r->stock, $r->mrp, $r->value]));
        }

        if ($request->query('format') === 'pdf') {
            return $this->pdfOut('Stock Report' . ($filter !== 'all' ? ' — ' . strtoupper($filter) : ''), 'As of ' . now()->format('d M Y'),
                [['Brand', 'l'], ['Product', 'l'], ['Stock', 'r'], ['MRP', 'r'], ['Value at MRP', 'r']],
                $rows->map(fn ($r) => [$r->brand, $r->name, (string) $r->stock, inr($r->mrp, 2), inr($r->value, 2)])->all(),
                ['Total (' . $totals['items'] . ' items)', '', inr($totals['units']), '', inr($totals['value'], 2)]);
        }

        return view('reports.stock', ['rows' => $rows, 'totals' => $totals, 'filter' => $filter]);
    }

    // ---------------------------------------------------------------
    // Purchase Register
    // ---------------------------------------------------------------
    public function purchases(Request $request)
    {
        [$from, $to, $fyActive] = $this->period($request);

        $bills = DB::table('supplier_bills')
            ->join('suppliers', 'suppliers.id', '=', 'supplier_bills.supplier_id')
            ->select('supplier_bills.bill_date', 'suppliers.firm_name', 'supplier_bills.bill_no',
                'supplier_bills.amount', 'supplier_bills.note')
            ->orderBy('bill_date');
        if ($from) $bills->where('bill_date', '>=', $from);
        if ($to) $bills->where('bill_date', '<=', $to);
        $bills = $bills->get();

        $inward = DB::table('inward_entries')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'inward_entries.supplier_id')
            ->leftJoin('inward_lines', 'inward_lines.inward_entry_id', '=', 'inward_entries.id')
            ->select('inward_entries.id', 'inward_entries.inward_date',
                DB::raw("COALESCE(suppliers.firm_name, '-') AS firm_name"),
                DB::raw('COUNT(inward_lines.id) AS line_count'),
                DB::raw('SUM(inward_lines.qty) AS units'),
                DB::raw('SUM(inward_lines.qty * COALESCE(inward_lines.purchase_rate, 0)) AS value'))
            ->groupBy('inward_entries.id', 'inward_entries.inward_date', 'firm_name')
            ->orderBy('inward_entries.inward_date');
        if ($from) $inward->where('inward_date', '>=', $from);
        if ($to) $inward->where('inward_date', '<=', $to);
        $inward = $inward->get();

        if ($request->query('format') === 'csv') {
            return $this->csv('purchase-register', ['Type', 'Date', 'Supplier', 'Ref', 'Units', 'Amount'],
                $bills->map(fn ($b) => ['Bill', $b->bill_date, $b->firm_name, $b->bill_no, '', $b->amount])
                    ->concat($inward->map(fn ($i) => ['Inward', $i->inward_date, $i->firm_name, 'Entry #' . $i->id, $i->units, $i->value])));
        }

        if ($request->query('format') === 'pdf') {
            $body = $bills->map(fn ($b) => ['Bill', Carbon::parse($b->bill_date)->format('d/m/y'), $b->firm_name, (string) ($b->bill_no ?? ''), '', inr($b->amount, 2)])
                ->concat($inward->map(fn ($i) => ['Inward', Carbon::parse($i->inward_date)->format('d/m/y'), $i->firm_name, 'Entry #' . $i->id, (string) $i->units, $i->value > 0 ? inr($i->value, 2) : '']))->all();
            return $this->pdfOut('Purchase Register', $this->periodLabel($from, $to),
                [['Type', 'l'], ['Date', 'l'], ['Supplier', 'l'], ['Ref', 'l'], ['Units', 'r'], ['Amount', 'r']],
                $body,
                ['Bills total: ' . inr($bills->sum('amount'), 2), '', '', 'Inward:', inr($inward->sum('units')), inr($inward->sum('value'), 2)],
                'Bills drive the supplier ledger; inward drives stock. Inward value counts only lines with a purchase rate.');
        }

        return view('reports.purchases', [
            'bills' => $bills, 'inward' => $inward,
            'billTotal' => $bills->sum('amount'),
            'inwardUnits' => $inward->sum('units'),
            'inwardValue' => $inward->sum('value'),
            'from' => $from, 'to' => $to, 'fyActive' => $fyActive,
        ]);
    }

    // ---------------------------------------------------------------
    // GST / HSN Summary
    // ---------------------------------------------------------------
    public function gst(Request $request)
    {
        [$from, $to, $fyActive] = $this->period($request);

        $q = DB::table('invoice_lines')
            ->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->where('invoice_lines.tax_percent', '>', 0)
            ->select(
                DB::raw("COALESCE(NULLIF(invoice_lines.hsn_code, ''), '-') AS hsn"),
                'invoice_lines.tax_percent AS pct',
                DB::raw('SUM(invoice_lines.amount) AS gross'),
                DB::raw('SUM(invoice_lines.amount / (1 + invoice_lines.tax_percent / 100)) AS taxable'))
            ->groupBy('hsn', 'pct')->orderBy('hsn');
        if ($from) $q->where('invoices.invoice_date', '>=', $from);
        if ($to) $q->where('invoices.invoice_date', '<=', $to);

        $rows = $q->get()->map(function ($r) {
            $taxable = round((float) $r->taxable, 2);
            $tax = round((float) $r->gross - $taxable, 2);
            return (object) [
                'hsn' => $r->hsn, 'pct' => (float) $r->pct, 'taxable' => $taxable,
                'cgst' => round($tax / 2, 2), 'sgst' => round($tax / 2, 2),
                'tax' => $tax, 'gross' => (float) $r->gross,
            ];
        });

        $exempt = DB::table('invoice_lines')
            ->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->where('invoice_lines.tax_percent', '=', 0);
        if ($from) $exempt->where('invoices.invoice_date', '>=', $from);
        if ($to) $exempt->where('invoices.invoice_date', '<=', $to);
        $exemptTotal = (float) $exempt->sum('invoice_lines.amount');

        if ($request->query('format') === 'csv') {
            return $this->csv('gst-hsn-summary', ['HSN', 'GST %', 'Taxable', 'CGST', 'SGST', 'Total Tax', 'Gross'],
                $rows->map(fn ($r) => [$r->hsn, $r->pct, $r->taxable, $r->cgst, $r->sgst, $r->tax, $r->gross]));
        }

        if ($request->query('format') === 'pdf') {
            $pctFmt = fn ($p) => rtrim(rtrim(inr($p, 2), '0'), '.') . '%';
            return $this->pdfOut('GST / HSN Summary', $this->periodLabel($from, $to),
                [['HSN', 'l'], ['GST %', 'r'], ['Taxable', 'r'], ['CGST', 'r'], ['SGST', 'r'], ['Total Tax', 'r'], ['Gross', 'r']],
                $rows->map(fn ($r) => [$r->hsn, $pctFmt($r->pct), inr($r->taxable, 2), inr($r->cgst, 2), inr($r->sgst, 2), inr($r->tax, 2), inr($r->gross, 2)])->all(),
                ['Total', '', inr($rows->sum('taxable'), 2), inr($rows->sum('tax') / 2, 2), inr($rows->sum('tax') / 2, 2), inr($rows->sum('tax'), 2), inr($rows->sum('gross'), 2)],
                'Taxable backed out of tax-inclusive amounts (taxable = amount / (1 + rate)).'
                . ($exemptTotal > 0 ? ' Zero-GST line value in period: Rs ' . inr($exemptTotal, 2) . '.' : ''));
        }

        return view('reports.gst', [
            'rows' => $rows, 'exemptTotal' => $exemptTotal,
            'sumTaxable' => $rows->sum('taxable'), 'sumTax' => $rows->sum('tax'), 'sumGross' => $rows->sum('gross'),
            'from' => $from, 'to' => $to, 'fyActive' => $fyActive,
        ]);
    }

    // ---------------------------------------------------------------
    private function period(Request $request): array
    {
        $preset = $request->query('fy');
        if ($preset) {
            $now = Carbon::today();
            $fyStart = $now->month >= 4 ? $now->year : $now->year - 1;
            if ($preset === 'this') return [$fyStart . '-04-01', null, 'this'];
            if ($preset === 'last') return [($fyStart - 1) . '-04-01', $fyStart . '-03-31', 'last'];
            if ($preset === 'thismonth') return [$now->format('Y-m-01'), null, 'thismonth'];
            if ($preset === 'q1') return [$fyStart . '-04-01', $fyStart . '-06-30', 'q1'];
            if ($preset === 'q2') return [$fyStart . '-07-01', $fyStart . '-09-30', 'q2'];
            if ($preset === 'q3') return [$fyStart . '-10-01', $fyStart . '-12-31', 'q3'];
            if ($preset === 'q4') return [($fyStart + 1) . '-01-01', ($fyStart + 1) . '-03-31', 'q4'];
        }
        $from = $request->query('from') ?: null;
        $to = $request->query('to') ?: null;
        return [$from, $to, ($from || $to) ? 'custom' : 'all'];
    }

    private function periodLabel(?string $from, ?string $to): string
    {
        if (!$from && !$to) return 'All transactions';
        $f = $from ? Carbon::parse($from)->format('d M Y') : 'Start';
        $t = $to ? Carbon::parse($to)->format('d M Y') : 'Today';
        return $f . ' – ' . $t;
    }

    private function pdfOut(string $title, string $period, array $columns, array $rows, ?array $totals = null, ?string $note = null)
    {
        $pdf = Pdf::loadView('reports.pdf', [
            's' => Setting::getAll(),
            'title' => $title,
            'period' => $period,
            'columns' => $columns,
            'rows' => $rows,
            'totals' => $totals,
            'note' => $note,
        ])->setPaper('a4');

        return $pdf->download(str_replace(['/', ' '], ['-', '-'], strtolower($title)) . '-' . now()->format('Ymd-His') . '.pdf');
    }

    private function csv(string $name, array $header, $rows): StreamedResponse
    {
        $filename = $name . '-' . now()->format('Ymd-His') . '.csv';
        return response()->streamDownload(function () use ($header, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);
            foreach ($rows as $r) {
                fputcsv($out, $r instanceof \Traversable ? iterator_to_array($r) : (array) $r);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
