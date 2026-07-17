<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class CreditNoteController extends Controller
{
    public function create(Partner $partner)
    {
        return view('creditnotes.create', ['partner' => $partner]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_id' => ['required', 'exists:partners,id'],
            'cn_date' => ['required', 'date'],
            'kind' => ['required', 'in:goods,amount'],
            'reason' => ['nullable', 'string', 'max:255'],
            'amount' => ['required_if:kind,amount', 'nullable', 'numeric', 'gt:0'],
            'lines' => ['required_if:kind,goods', 'nullable', 'array'],
            'lines.*.product_id' => ['required', 'exists:products,id'],
            'lines.*.qty' => ['required', 'integer', 'min:1'],
            'lines.*.rate' => ['required', 'numeric', 'gt:0'],
        ]);

        $cn = DB::transaction(function () use ($data) {
            $nextNo = (int) CreditNote::lockForUpdate()->max('cn_no') + 1;

            if ($data['kind'] === 'amount') {
                return CreditNote::create([
                    'cn_no' => $nextNo,
                    'partner_id' => $data['partner_id'],
                    'cn_date' => $data['cn_date'],
                    'kind' => 'amount',
                    'reason' => $data['reason'] ?? null,
                    'total' => round((float) $data['amount'], 2),
                ]);
            }

            $productIds = collect($data['lines'])->pluck('product_id')->unique();
            $products = Product::with(['brand', 'category'])
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            $rows = [];

            foreach ($data['lines'] as $l) {
                $product = $products[$l['product_id']];
                $qty = (int) $l['qty'];
                $rate = round((float) $l['rate'], 2);
                $amount = round($qty * $rate, 2);
                $total += $amount;

                $rows[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'brand' => $product->brand->name,
                    'category' => $product->category->name,
                    'mrp' => $product->mrp,
                    'qty' => $qty,
                    'rate' => $rate,
                    'amount' => $amount,
                ];

                // Returned goods go back on the shelf
                $product->increment('stock_qty', $qty);
            }

            $cn = CreditNote::create([
                'cn_no' => $nextNo,
                'partner_id' => $data['partner_id'],
                'cn_date' => $data['cn_date'],
                'kind' => 'goods',
                'reason' => $data['reason'] ?? null,
                'total' => round($total, 2),
            ]);

            $cn->lines()->createMany($rows);

            return $cn;
        });

        return response()->json([
            'ok' => true,
            'id' => $cn->id,
            'cn_no' => $cn->cn_no,
            'url' => route('creditnotes.show', $cn),
        ]);
    }

    public function show(CreditNote $creditNote)
    {
        $this->loadCn($creditNote);
        $s = Setting::getAll();
        $publicUrl = URL::signedRoute('creditnotes.public', ['creditNote' => $creditNote]);

        return view('creditnotes.show', [
            'cn' => $creditNote,
            's' => $s,
            'public' => false,
            'publicUrl' => $publicUrl,
            'pdfUrl' => route('creditnotes.pdf', $creditNote),
            'waText' => $this->draftWhatsApp($creditNote, $s, $publicUrl),
        ]);
    }

    public function publicShow(CreditNote $creditNote)
    {
        $this->loadCn($creditNote);

        return view('creditnotes.show', [
            'cn' => $creditNote,
            's' => Setting::getAll(),
            'public' => true,
            'publicUrl' => null,
            'pdfUrl' => URL::signedRoute('creditnotes.public.pdf', ['creditNote' => $creditNote]),
            'waText' => null,
        ]);
    }

    public function pdf(CreditNote $creditNote)
    {
        return $this->renderPdf($creditNote);
    }

    public function publicPdf(CreditNote $creditNote)
    {
        return $this->renderPdf($creditNote);
    }

    private function renderPdf(CreditNote $creditNote)
    {
        $this->loadCn($creditNote);

        $pdf = Pdf::loadView('creditnotes.pdf', [
            'cn' => $creditNote,
            's' => Setting::getAll(),
        ])->setPaper('a4');

        return $pdf->download('CN-' . $creditNote->cn_no . '.pdf');
    }

    private function loadCn(CreditNote $creditNote): void
    {
        $creditNote->load(['partner', 'lines' => fn ($q) => $q->orderBy('brand')->orderBy('category')->orderBy('name')]);
    }

    private function draftWhatsApp(CreditNote $cn, array $s, string $publicUrl): string
    {
        $solid = "\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}";
        $dotted = "\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}";
        $rs = "\u{20B9}";

        $t = [];
        $t[] = '*' . mb_strtoupper($s['firm_name'] ?? 'CREDIT NOTE') . '*';
        $meta = [];
        if (!empty($s['firm_gst'])) $meta[] = 'GSTIN ' . $s['firm_gst'];
        if (!empty($s['firm_mobile'])) $meta[] = 'Mob ' . $s['firm_mobile'];
        if ($meta) $t[] = implode(' | ', $meta);
        $t[] = $solid;
        $t[] = '*CREDIT NOTE CN-' . $cn->cn_no . '*';
        $t[] = 'Date: ' . $cn->cn_date->format('d M Y');
        $t[] = 'To: *' . $cn->partner->firm_name . '*';
        if ($cn->reason) $t[] = 'Reason: ' . $cn->reason;
        $t[] = $solid;

        if ($cn->kind === 'goods') {
            $t[] = '*RETURNED ITEMS*';
            $n = 0;
            foreach ($cn->lines as $line) {
                $n++;
                if ($n > 1) $t[] = $dotted;
                $t[] = $n . '. ' . $line->name;
                $t[] = '    ' . $line->qty . ' x ' . $rs . number_format($line->rate, 2)
                    . ' = ' . $rs . number_format($line->amount, 2);
            }
            $t[] = $dotted;
        }

        $t[] = '*AMOUNT CREDITED: ' . $rs . number_format($cn->total, 2) . '*';
        $t[] = 'Credited to the account of ' . $cn->partner->firm_name . '. Balance payable to ' . ($s['firm_name'] ?? 'us') . ' stands reduced.';
        $t[] = $solid;
        $t[] = 'View & download PDF:';
        $t[] = $publicUrl;

        return implode("\n", $t);
    }
}
