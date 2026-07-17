<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\Partner;
use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_id' => ['required', 'exists:partners,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'method' => ['required', 'in:cash,upi,bank,cheque,other'],
            'reference' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $payment = Payment::create($data);

        return response()->json(['ok' => true, 'id' => $payment->id]);
    }

    public function ledger(Partner $partner)
    {
        $s = Setting::getAll();
        $publicUrl = URL::signedRoute('ledger.public', ['partner' => $partner]);

        return view('ledger.show', [
            'partner' => $partner,
            's' => $s,
            'public' => false,
            'publicUrl' => $publicUrl,
            'dataUrl' => route('ledger.data', $partner),
        ]);
    }

    public function data(Partner $partner, Request $request)
    {
        $ledger = $this->buildLedger($partner, $request->query('from'), $request->query('to'));

        $s = Setting::getAll();
        $publicUrl = URL::signedRoute('ledger.public', array_filter([
            'partner' => $partner->id,
            'from' => $request->query('from'),
            'to' => $request->query('to'),
        ]));
        $ledger['wa_text'] = $this->draftWhatsApp($partner, $s, $ledger, $publicUrl, $request->query('from'), $request->query('to'));
        $ledger['pdf_url'] = route('ledger.pdf', ['partner' => $partner, 'from' => $request->query('from'), 'to' => $request->query('to')]);

        return response()->json($ledger);
    }

    public function pdf(Partner $partner, Request $request)
    {
        return $this->renderPdf($partner, $request->query('from'), $request->query('to'));
    }

    public function publicShow(Partner $partner, Request $request)
    {
        return view('ledger.show', [
            'partner' => $partner,
            's' => Setting::getAll(),
            'public' => true,
            'publicUrl' => null,
            'dataUrl' => URL::signedRoute('ledger.public.data', array_filter([
                'partner' => $partner->id,
                'from' => $request->query('from'),
                'to' => $request->query('to'),
            ])),
        ]);
    }

    public function publicData(Partner $partner, Request $request)
    {
        $ledger = $this->buildLedger($partner, $request->query('from'), $request->query('to'));
        $ledger['wa_text'] = null;
        $ledger['from'] = $request->query('from');
        $ledger['to'] = $request->query('to');
        $ledger['pdf_url'] = URL::signedRoute('ledger.public.pdf', array_filter([
            'partner' => $partner->id,
            'from' => $request->query('from'),
            'to' => $request->query('to'),
        ]));

        return response()->json($ledger);
    }

    public function publicPdf(Partner $partner, Request $request)
    {
        return $this->renderPdf($partner, $request->query('from'), $request->query('to'));
    }

    private function renderPdf(Partner $partner, ?string $from, ?string $to)
    {
        $ledger = $this->buildLedger($partner, $from, $to);
        $s = Setting::getAll();

        $qrDataUri = null;
        if (!empty($s['upi_id'])) {
            $upiLink = 'upi://pay?pa=' . rawurlencode($s['upi_id'])
                . '&pn=' . rawurlencode($s['firm_name'] ?? 'Payment')
                . '&cu=INR&tn=' . rawurlencode('Ledger payment');

            $options = new QROptions;
            $options->outputInterface = QRGdImagePNG::class;
            $options->scale = 4;
            $options->outputBase64 = true;
            $options->quietzoneSize = 1;
            $qrDataUri = (new QRCode($options))->render($upiLink);
        }

        $pdf = Pdf::loadView('ledger.pdf', [
            'partner' => $partner,
            's' => $s,
            'ledger' => $ledger,
            'from' => $from,
            'to' => $to,
            'qrDataUri' => $qrDataUri,
        ])->setPaper('a4');

        return $pdf->download('Ledger-' . preg_replace('/[^A-Za-z0-9]+/', '-', $partner->firm_name) . '.pdf');
    }

    private function buildLedger(Partner $partner, ?string $from, ?string $to): array
    {
        $opening = 0.0;
        if ($from) {
            $opening = (float) Invoice::where('partner_id', $partner->id)->whereDate('invoice_date', '<', $from)->sum('total')
                - (float) Payment::where('partner_id', $partner->id)->whereDate('payment_date', '<', $from)->sum('amount')
                - (float) CreditNote::where('partner_id', $partner->id)->whereDate('cn_date', '<', $from)->sum('total');
        }

        $invoices = Invoice::where('partner_id', $partner->id)
            ->when($from, fn ($q) => $q->whereDate('invoice_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('invoice_date', '<=', $to))
            ->get()
            ->map(fn (Invoice $i) => [
                'date' => $i->invoice_date->format('Y-m-d'),
                'sort' => $i->invoice_date->format('Y-m-d') . '-1-' . str_pad($i->id, 10, '0', STR_PAD_LEFT),
                'type' => 'invoice',
                'label' => 'INV-' . $i->invoice_no,
                'detail' => $i->lines()->count() . ' items',
                'debit' => (float) $i->total,
                'credit' => 0.0,
                'url' => route('invoices.show', $i),
            ]);

        $payments = Payment::where('partner_id', $partner->id)
            ->when($from, fn ($q) => $q->whereDate('payment_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('payment_date', '<=', $to))
            ->get()
            ->map(fn (Payment $p) => [
                'date' => $p->payment_date->format('Y-m-d'),
                'sort' => $p->payment_date->format('Y-m-d') . '-2-' . str_pad($p->id, 10, '0', STR_PAD_LEFT),
                'type' => 'payment',
                'label' => 'Payment (' . strtoupper($p->method) . ')',
                'detail' => trim(($p->reference ? $p->reference . ' ' : '') . ($p->note ?: '')),
                'debit' => 0.0,
                'credit' => (float) $p->amount,
                'url' => null,
            ]);

        $creditNotes = CreditNote::where('partner_id', $partner->id)
            ->when($from, fn ($q) => $q->whereDate('cn_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('cn_date', '<=', $to))
            ->get()
            ->map(fn (CreditNote $cn) => [
                'date' => $cn->cn_date->format('Y-m-d'),
                'sort' => $cn->cn_date->format('Y-m-d') . '-3-' . str_pad($cn->id, 10, '0', STR_PAD_LEFT),
                'type' => 'creditnote',
                'label' => 'CN-' . $cn->cn_no . ($cn->kind === 'goods' ? ' (goods return)' : ''),
                'detail' => $cn->reason ?: '',
                'debit' => 0.0,
                'credit' => (float) $cn->total,
                'url' => route('creditnotes.show', $cn),
            ]);
        $entries = $invoices->concat($payments)->concat($creditNotes)->sortBy('sort')->values();

        $balance = $opening;
        $entries = $entries->map(function ($e) use (&$balance) {
            $balance += $e['debit'] - $e['credit'];
            $e['balance'] = round($balance, 2);
            return $e;
        });

        return [
            'partner' => [
                'id' => $partner->id,
                'firm_name' => $partner->firm_name,
                'mobile' => $partner->mobile,
            ],
            'opening' => round($opening, 2),
            'entries' => $entries,
            'total_debit' => round($entries->sum('debit'), 2),
            'total_credit' => round($entries->sum('credit'), 2),
            'closing' => round($balance, 2),
        ];
    }

    private function draftWhatsApp(Partner $partner, array $s, array $ledger, string $publicUrl, ?string $from, ?string $to): string
    {
        $solid = "\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}";
        $dotted = "\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}";
        $rs = "\u{20B9}";

        $t = [];
        $t[] = '*' . mb_strtoupper($s['firm_name'] ?? 'STATEMENT') . '*';
        $meta = [];
        if (!empty($s['firm_gst'])) $meta[] = 'GSTIN ' . $s['firm_gst'];
        if (!empty($s['firm_mobile'])) $meta[] = 'Mob ' . $s['firm_mobile'];
        if ($meta) $t[] = implode(' | ', $meta);
        $t[] = $solid;
        $t[] = '*ACCOUNT STATEMENT*';
        $t[] = 'For: *' . $partner->firm_name . '*';
        if ($from || $to) {
            $t[] = 'Period: ' . ($from ? date('d M Y', strtotime($from)) : 'Start') . ' to ' . ($to ? date('d M Y', strtotime($to)) : 'Today');
        }
        $t[] = $solid;
        if ($from) {
            $t[] = 'Opening balance: ' . $rs . number_format($ledger['opening'], 2);
        }
        $t[] = 'Total bills: ' . $rs . number_format($ledger['total_debit'], 2);
        $t[] = 'Total paid: ' . $rs . number_format($ledger['total_credit'], 2);
        $t[] = $dotted;
        if ($ledger['closing'] > 0) {
            $t[] = '*BALANCE DUE: ' . $rs . number_format($ledger['closing'], 2) . '*';
        } elseif ($ledger['closing'] < 0) {
            $t[] = '*ADVANCE WITH US: ' . $rs . number_format(abs($ledger['closing']), 2) . '*';
        } else {
            $t[] = '*BALANCE: ' . $rs . '0.00 (settled)*';
        }

        $hasPay = !empty($s['upi_id']) || (!empty($s['bank_account']) && !empty($s['bank_ifsc']));
        if ($hasPay && $ledger['closing'] > 0) {
            $t[] = $dotted;
            $t[] = '*PAYMENT*';
            if (!empty($s['upi_id'])) $t[] = 'UPI: ' . $s['upi_id'];
            if (!empty($s['bank_name'])) $t[] = $s['bank_name'];
            if (!empty($s['bank_account']) && !empty($s['bank_ifsc'])) {
                $t[] = 'A/c ' . $s['bank_account'] . ' | IFSC ' . $s['bank_ifsc'];
            }
            if (!empty($s['bank_holder'])) $t[] = 'Name: ' . $s['bank_holder'];
        }

        $t[] = $solid;
        $t[] = 'View full ledger & download PDF:';
        $t[] = $publicUrl;
        $t[] = '';
        $t[] = 'Thank you for your business!';

        return implode("\n", $t);
    }
}
