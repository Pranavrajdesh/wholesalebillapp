<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\RateGroup;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class InvoiceController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_id' => ['required', 'exists:partners,id'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'invoice_date' => ['required', 'date'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'exists:products,id'],
            'lines.*.qty' => ['required', 'integer', 'min:1'],
            'lines.*.free_qty' => ['nullable', 'integer', 'min:0'],
            'lines.*.scheme_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lines.*.rate' => ['required', 'numeric', 'gt:0'],
            'lines.*.manual_rate' => ['nullable', 'boolean'],
            'lines.*.manual_free' => ['nullable', 'boolean'],
            'discount.type' => ['nullable', 'in:amount,percent'],
            'discount.value' => ['nullable', 'numeric', 'min:0'],
            'discount.note' => ['nullable', 'string', 'max:255'],
        ]);

        $groupId = RateGroup::firstOrCreate(['name' => 'General'])->id;
        $allowNegative = (Setting::getAll()['allow_negative_stock'] ?? '1') === '1';

        $invoice = DB::transaction(function () use ($data, $groupId, $allowNegative) {
            $productIds = collect($data['lines'])->pluck('product_id')->unique();
            $products = Product::with(['brand', 'category', 'rateSlabs' => fn ($q) => $q->where('rate_group_id', $groupId)->orderBy('min_qty')])
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $lineRows = [];
            $stockIssues = [];

            foreach ($data['lines'] as $l) {
                $product = $products[$l['product_id']];
                $qty = (int) $l['qty'];
                $schemePercent = (float) ($l['scheme_percent'] ?? 0);
                $manualRate = (bool) ($l['manual_rate'] ?? false);
                $manualFree = (bool) ($l['manual_free'] ?? false);

                $slab = $product->rateSlabs->last(fn ($s) => $qty >= $s->min_qty);

                if ($manualRate || !$slab) {
                    $rate = round((float) $l['rate'], 2);
                } else {
                    $rate = round((float) $slab->rate * (1 - $schemePercent / 100), 2);
                }

                if ($manualFree || !$slab) {
                    $freeQty = (int) ($l['free_qty'] ?? 0);
                } else {
                    $freeQty = ($slab->offer_buy_qty && $slab->offer_free_qty)
                        ? intdiv($qty, $slab->offer_buy_qty) * $slab->offer_free_qty
                        : 0;
                }

                $amount = round($qty * $rate, 2);
                if (!$product->tax_inclusive && $product->tax_percent > 0) {
                    // Exclusive rate: tax added on top so the bill total is honest
                    $amount = round($amount * (1 + $product->tax_percent / 100), 2);
                }
                $subtotal += $amount;

                $lineRows[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'brand' => $product->brand->name,
                    'category' => $product->category->name,
                    'hsn_code' => $product->hsn_code,
                    'mrp' => $product->mrp,
                    'qty' => $qty,
                    'free_qty' => $freeQty,
                    'rate' => $rate,
                    'scheme_percent' => $schemePercent,
                    'tax_percent' => $product->tax_percent,
                    'tax_inclusive' => $product->tax_inclusive,
                    'amount' => $amount,
                ];

                if (!$allowNegative && ($qty + $freeQty) > (int) $product->stock_qty) {
                    $stockIssues[] = $product->name . ' (need ' . ($qty + $freeQty) . ', in stock ' . (int) $product->stock_qty . ')';
                }
                $product->decrement('stock_qty', $qty + $freeQty);
            }

            if ($stockIssues) {
                abort(response()->json([
                    'ok' => false,
                    'message' => 'Insufficient stock: ' . implode('; ', $stockIssues) . '. Enable "billing beyond stock" in Settings to override.',
                ], 422));
            }

            $subtotal = round($subtotal, 2);

            $discountType = $data['discount']['type'] ?? null;
            $discountValue = round((float) ($data['discount']['value'] ?? 0), 2);
            $discountAmount = 0;

            if ($discountType === 'percent') {
                $discountAmount = round($subtotal * $discountValue / 100, 2);
            } elseif ($discountType === 'amount') {
                $discountAmount = $discountValue;
            }

            $discountAmount = min(max($discountAmount, 0), $subtotal);
            $net = $subtotal - $discountAmount;
            $total = round($net);
            $roundOff = round($total - $net, 2);

            $nextNo = (int) Invoice::lockForUpdate()->max('invoice_no') + 1;

            $invoice = Invoice::create([
                'invoice_no' => $nextNo,
                'partner_id' => $data['partner_id'],
                'invoice_date' => $data['invoice_date'],
                'subtotal' => $subtotal,
                'discount_type' => $discountAmount > 0 ? $discountType : null,
                'discount_value' => $discountAmount > 0 ? $discountValue : 0,
                'discount_amount' => $discountAmount,
                'discount_note' => $data['discount']['note'] ?? null,
                'round_off' => $roundOff,
                'total' => $total,
            ]);

            $invoice->lines()->createMany($lineRows);

            if (!empty($data['order_id'])) {
                Order::where('id', $data['order_id'])
                    ->where('status', 'pending')
                    ->update(['status' => 'invoiced', 'invoice_id' => $invoice->id]);
            }

            return $invoice;
        });

        return response()->json([
            'ok' => true,
            'id' => $invoice->id,
            'invoice_no' => $invoice->invoice_no,
            'url' => route('invoices.show', $invoice),
        ]);
    }

    public function index()
    {
        return view('invoices.index');
    }

    public function data(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $from = $request->query('from');
        $to = $request->query('to');
        $offset = max(0, (int) $request->query('offset', 0));
        $limit = min(50, max(1, (int) $request->query('limit', 25)));

        $numeric = ctype_digit(ltrim(strtoupper($q), 'INV-')) && $q !== '';

        $query = Invoice::with('partner')
            ->withCount('lines')
            ->when($q !== '', function ($x) use ($q, $numeric) {
                $x->where(function ($w2) use ($q, $numeric) {
                    $w2->whereHas('partner', fn ($p) => $p->where('firm_name', 'like', '%' . $q . '%'));
                    if ($numeric) {
                        $w2->orWhere('invoice_no', (int) ltrim(strtoupper($q), 'INV-'));
                    }
                });
            })
            ->when($from, fn ($x) => $x->whereDate('invoice_date', '>=', $from))
            ->when($to, fn ($x) => $x->whereDate('invoice_date', '<=', $to))
            ->orderByDesc('invoice_no');

        $total = (clone $query)->count();
        $items = $query->skip($offset)->take($limit)->get()->map(fn (Invoice $i) => [
            'id' => $i->id,
            'invoice_no' => $i->invoice_no,
            'date' => $i->invoice_date->format('d M Y'),
            'firm_name' => $i->partner->firm_name,
            'line_count' => $i->lines_count,
            'total' => (float) $i->total,
            'url' => route('invoices.show', $i),
        ]);

        return response()->json([
            'items' => $items,
            'total' => $total,
            'next_offset' => $offset + $items->count(),
            'has_more' => $offset + $items->count() < $total,
        ]);
    }

    public function show(Invoice $invoice)
    {
        $this->loadInvoice($invoice);
        $s = Setting::getAll();
        $publicUrl = URL::signedRoute('invoices.public', ['invoice' => $invoice]);

        return view('invoices.show', [
            'invoice' => $invoice,
            's' => $s,
            'public' => false,
            'publicUrl' => $publicUrl,
            'pdfUrl' => route('invoices.pdf', $invoice),
            'waText' => $this->draftWhatsApp($invoice, $s, $publicUrl),
        ]);
    }

    public function publicShow(Invoice $invoice)
    {
        $this->loadInvoice($invoice);

        return view('invoices.show', [
            'invoice' => $invoice,
            's' => Setting::getAll(),
            'public' => true,
            'publicUrl' => null,
            'pdfUrl' => URL::signedRoute('invoices.public.pdf', ['invoice' => $invoice]),
            'waText' => null,
        ]);
    }

    public function pdf(Invoice $invoice)
    {
        return $this->renderPdf($invoice);
    }

    public function publicPdf(Invoice $invoice)
    {
        return $this->renderPdf($invoice);
    }

    private function renderPdf(Invoice $invoice)
    {
        $this->loadInvoice($invoice);
        $s = Setting::getAll();

        $qrDataUri = null;
        if (!empty($s['upi_id'])) {
            $upiLink = 'upi://pay?pa=' . rawurlencode($s['upi_id'])
                . '&pn=' . rawurlencode($s['firm_name'] ?? 'Payment')
                . '&cu=INR&tn=' . rawurlencode('INV-' . $invoice->invoice_no);

            $options = new QROptions;
            $options->outputInterface = QRGdImagePNG::class;
            $options->scale = 4;
            $options->outputBase64 = true;
            $options->quietzoneSize = 1;
            $qrDataUri = (new QRCode($options))->render($upiLink);
        }

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            's' => $s,
            'qrDataUri' => $qrDataUri,
        ])->setPaper('a4');

        return $pdf->download('INV-' . $invoice->invoice_no . '.pdf');
    }

    private function loadInvoice(Invoice $invoice): void
    {
        $invoice->load(['partner', 'lines' => fn ($q) => $q->orderBy('brand')->orderBy('category')->orderBy('name')]);
    }

    private function draftWhatsApp(Invoice $invoice, array $s, string $publicUrl): string
    {
        $solid = "\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}\u{2501}";
        $dotted = "\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}\u{2504}";
        $rs = "\u{20B9}";

        $t = [];
        $t[] = '*' . mb_strtoupper($s['firm_name'] ?? 'INVOICE') . '*';
        $meta = [];
        if (!empty($s['firm_gst'])) $meta[] = 'GSTIN ' . $s['firm_gst'];
        if (!empty($s['firm_mobile'])) $meta[] = 'Mob ' . $s['firm_mobile'];
        if ($meta) $t[] = implode(' | ', $meta);
        $t[] = $solid;
        $t[] = '*INVOICE INV-' . $invoice->invoice_no . '*';
        $t[] = 'Date: ' . $invoice->invoice_date->format('d M Y');
        $t[] = 'To: *' . $invoice->partner->firm_name . '*';
        $t[] = $solid;
        $t[] = '*ITEMS*';

        $n = 0;
        foreach ($invoice->lines as $line) {
            $n++;
            if ($n > 1) $t[] = $dotted;
            $t[] = $n . '. ' . $line->name;
            $row = '    ' . $line->qty . ' x ' . $rs . number_format($line->rate, 2)
                . ' = ' . $rs . number_format($line->amount, 2);
            if ($line->free_qty > 0) {
                $row .= ' (+' . $line->free_qty . ' free)';
            }
            $t[] = $row;
        }

        $t[] = $dotted;
        $t[] = 'Subtotal: ' . $rs . number_format($invoice->subtotal, 2);
        if ($invoice->discount_amount > 0) {
            $t[] = 'Discount: -' . $rs . number_format($invoice->discount_amount, 2)
                . ($invoice->discount_note ? ' (' . $invoice->discount_note . ')' : '');
        }
        if (abs((float) $invoice->round_off) >= 0.01) {
            $t[] = 'Round off: ' . ($invoice->round_off >= 0 ? '+' : '-') . $rs . number_format(abs($invoice->round_off), 2);
        }
        $t[] = '*TOTAL: ' . $rs . number_format($invoice->total, 2) . '*';

        $mrpValue = $invoice->lines->sum(fn ($l) => $l->mrp * ($l->qty + $l->free_qty));
        $cost = (float) $invoice->total;
        if ($mrpValue > 0 && $cost > 0) {
            $profit = $mrpValue - $cost;
            $t[] = $dotted;
            $t[] = '*YOUR PROFIT (at MRP)*';
            $t[] = 'Stock value: ' . $rs . number_format($mrpValue, 2);
            $t[] = 'Profit: ' . $rs . number_format($profit, 2)
                . ' (' . number_format($profit / $cost * 100, 1) . '% | margin ' . number_format($mrpValue / $cost, 2) . ')';
        }

        $hasPay = !empty($s['upi_id']) || (!empty($s['bank_account']) && !empty($s['bank_ifsc']));
        if ($hasPay) {
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
        $t[] = 'View & download PDF:';
        $t[] = $publicUrl;
        $t[] = '';
        $t[] = 'Thank you for your business!';

        return implode("\n", $t);
    }
}
