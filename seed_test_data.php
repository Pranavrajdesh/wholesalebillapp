<?php

/**
 * Test-data seeder for wholesaleBillApp.
 * Run once from the project root:  php seed_test_data.php
 * Additive: existing partners, invoices, and stock levels are preserved.
 * Guarded: refuses to run twice (checks for the marker partner mobile).
 *
 * NOTE: Seeded invoices intentionally do NOT decrement product stock —
 * they are historical records for ledger/list testing. Inward entries DO
 * increment stock (small quantities), matching real behavior.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\InwardEntry;
use App\Models\Order;
use App\Models\Partner;
use App\Models\Payment;
use App\Models\Product;
use App\Models\RateGroup;
use App\Models\Supplier;
use App\Models\SupplierBill;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;

mt_srand(20260715);

if (Partner::where('mobile', '9000000001')->exists()) {
    echo "Seed marker found (partner 9000000001) - already seeded. Aborting.\n";
    exit(1);
}

$groupId = RateGroup::firstOrCreate(['name' => 'General'])->id;

$products = Product::with(['brand', 'category', 'rateSlabs' => fn ($q) => $q->where('rate_group_id', $groupId)->orderBy('min_qty')])
    ->where('is_active', true)
    ->get();

if ($products->count() < 5) {
    echo "Need at least 5 active products. Aborting.\n";
    exit(1);
}

function lineRate($product): float
{
    $slab = $product->rateSlabs->first();
    return $slab ? (float) $slab->rate : round($product->mrp * 0.75, 2);
}

function d(string $date): string { return $date; }

DB::transaction(function () use ($products, $groupId) {

    // ---------- Partners ----------
    $partnerDefs = [
        ['Sharma Kirana & General', 'Rajesh Sharma', '9000000001', '27ABCPS1234F1Z5', 'Main Road, Sinnar', 1, 1, 1],
        ['Mauli Super Shoppe', 'Sunita Patil', '9000000002', null, 'Shivaji Chowk, Sinnar', 1, 1, 1],
        ['Balaji Traders', 'Venkatesh Rao', '9000000003', '27AAACB9876K1Z2', 'Market Yard, Nashik', 1, 0, 1], // prices hidden
        ['New Ganesh Stores', 'Ganesh More', '9000000004', null, 'Pune Road, Sinnar', 0, 1, 1], // portal off
        ['Old City Mart (Closed)', 'Imran Shaikh', '9000000005', null, 'Old Bazar, Sinnar', 1, 1, 0], // inactive
    ];

    $partners = collect();
    foreach ($partnerDefs as [$firm, $contact, $mobile, $gst, $addr, $portal, $prices, $active]) {
        $partners->push(Partner::create([
            'firm_name' => $firm,
            'contact_name' => $contact,
            'mobile' => $mobile,
            'gst_number' => $gst,
            'address' => $addr,
            'rate_group_id' => $groupId,
            'portal_access' => $portal,
            'show_prices' => $prices,
            'is_active' => $active,
        ]));
    }

    // Existing partners join the billing pool too
    $billingPool = Partner::where('is_active', true)->get();

    // ---------- Invoices + payments across two FYs ----------
    $dates = [];
    // FY 2025-26: Apr 2025 - Mar 2026 (22 invoices)
    foreach ([['2025-04', 2], ['2025-05', 2], ['2025-06', 2], ['2025-07', 2], ['2025-08', 2], ['2025-09', 2],
              ['2025-10', 2], ['2025-11', 2], ['2025-12', 2], ['2026-01', 2], ['2026-02', 1], ['2026-03', 1]] as [$ym, $n]) {
        for ($i = 0; $i < $n; $i++) $dates[] = $ym . '-' . str_pad(mt_rand(2, 27), 2, '0', STR_PAD_LEFT);
    }
    // FY 2026-27 so far: Apr - Jul 2026 (13 invoices)
    foreach ([['2026-04', 4], ['2026-05', 3], ['2026-06', 3], ['2026-07', 3]] as [$ym, $n]) {
        for ($i = 0; $i < $n; $i++) $dates[] = $ym . '-' . str_pad(mt_rand(2, 14), 2, '0', STR_PAD_LEFT);
    }
    sort($dates);

    $nextNo = (int) Invoice::max('invoice_no') + 1;
    $invoiceCount = 0;

    foreach ($dates as $date) {
        $partner = $billingPool[mt_rand(0, $billingPool->count() - 1)];
        $lineCount = mt_rand(2, 5);
        $picked = $products->random($lineCount);

        $subtotal = 0;
        $rows = [];
        foreach ($picked as $p) {
            $qty = [6, 12, 12, 24, 24, 48][mt_rand(0, 5)];
            $rate = lineRate($p);
            $amount = round($qty * $rate, 2);
            if (!$p->tax_inclusive && $p->tax_percent > 0) {
                $amount = round($amount * (1 + $p->tax_percent / 100), 2);
            }
            $subtotal += $amount;
            $rows[] = [
                'product_id' => $p->id,
                'name' => $p->name,
                'brand' => $p->brand->name,
                'category' => $p->category->name,
                'hsn_code' => $p->hsn_code,
                'mrp' => $p->mrp,
                'qty' => $qty,
                'free_qty' => 0,
                'rate' => $rate,
                'scheme_percent' => 0,
                'tax_percent' => $p->tax_percent,
                'tax_inclusive' => $p->tax_inclusive,
                'amount' => $amount,
            ];
        }

        $subtotal = round($subtotal, 2);
        $rounded = round($subtotal);
        $roundOff = round($rounded - $subtotal, 2);

        $invoice = Invoice::create([
            'invoice_no' => $nextNo++,
            'partner_id' => $partner->id,
            'invoice_date' => $date,
            'subtotal' => $subtotal,
            'discount_type' => null,
            'discount_value' => 0,
            'discount_amount' => 0,
            'round_off' => $roundOff,
            'total' => $rounded,
        ]);
        $invoice->lines()->createMany($rows);
        $invoiceCount++;

        // ~65% of invoices get a payment 3-20 days later (80-100% of total)
        if (mt_rand(1, 100) <= 65) {
            $payDate = date('Y-m-d', strtotime($date . ' +' . mt_rand(3, 20) . ' days'));
            if ($payDate <= '2026-07-15') {
                $fraction = [1.0, 1.0, 0.8, 0.9][mt_rand(0, 3)];
                Payment::create([
                    'partner_id' => $partner->id,
                    'payment_date' => $payDate,
                    'amount' => round($invoice->total * $fraction, 2),
                    'method' => ['cash', 'upi', 'bank', 'cheque'][mt_rand(0, 3)],
                    'reference' => mt_rand(0, 1) ? 'UTR' . mt_rand(100000, 999999) : null,
                    'note' => null,
                ]);
            }
        }
    }

    // ---------- Credit notes (amount-only; no stock side effects) ----------
    $cnNo = (int) CreditNote::max('cn_no') + 1;
    $cnDefs = [
        [$billingPool[0]->id, '2025-09-14', 350.00, 'Rate difference on Sept supply'],
        [$billingPool[mt_rand(0, $billingPool->count() - 1)]->id, '2026-01-20', 780.00, 'Damaged goods settlement'],
        [$billingPool[mt_rand(0, $billingPool->count() - 1)]->id, '2026-05-11', 500.00, 'Scheme adjustment'],
    ];
    foreach ($cnDefs as [$pid, $date, $amount, $reason]) {
        CreditNote::create([
            'cn_no' => $cnNo++,
            'partner_id' => $pid,
            'cn_date' => $date,
            'kind' => 'amount',
            'reason' => $reason,
            'total' => $amount,
        ]);
    }

    // ---------- Suppliers + their ledgers ----------
    $sup1 = Supplier::firstOrCreate(['mobile' => '9000000011'], [
        'firm_name' => 'Nashik FMCG Distributors',
        'contact_name' => 'Prakash Jain',
        'gst_number' => '27AABCN4321Q1Z8',
        'address' => 'MIDC, Nashik',
        'is_active' => true,
    ]);
    $sup2 = Supplier::firstOrCreate(['mobile' => '9000000012'], [
        'firm_name' => 'Pune Wholesale Agency',
        'contact_name' => 'Deepak Kulkarni',
        'gst_number' => null,
        'address' => 'Market Yard, Pune',
        'is_active' => true,
    ]);

    foreach ([$sup1, $sup2] as $si => $sup) {
        $billDates = $si === 0
            ? ['2025-06-10', '2025-09-05', '2025-12-12', '2026-03-08', '2026-05-20', '2026-07-02']
            : ['2025-08-15', '2026-01-10', '2026-06-05'];
        foreach ($billDates as $bi => $bd) {
            $amt = mt_rand(8, 45) * 500;
            SupplierBill::create([
                'supplier_id' => $sup->id,
                'bill_no' => strtoupper(substr($sup->firm_name, 0, 2)) . '-' . mt_rand(1000, 9999),
                'bill_date' => $bd,
                'amount' => $amt,
                'note' => $bi === 0 ? 'Opening season stock' : null,
            ]);
            // Most bills paid within a month
            if (mt_rand(1, 100) <= 75) {
                SupplierPayment::create([
                    'supplier_id' => $sup->id,
                    'payment_date' => date('Y-m-d', strtotime($bd . ' +' . mt_rand(7, 30) . ' days')),
                    'amount' => round($amt * ([1.0, 1.0, 0.5][mt_rand(0, 2)]), 2),
                    'method' => ['bank', 'upi', 'cheque'][mt_rand(0, 2)],
                    'reference' => 'UTR' . mt_rand(100000, 999999),
                    'note' => null,
                ]);
            }
        }
    }

    // ---------- Inward entries (these DO increment stock, small qtys) ----------
    foreach ([['2026-06-20', $sup1->id, 'Mid-June replenishment'], ['2026-07-05', $sup2->id, null], ['2026-07-12', null, 'Local cash purchase']] as [$date, $supId, $note]) {
        $entry = InwardEntry::create(['inward_date' => $date, 'supplier_id' => $supId, 'note' => $note]);
        foreach ($products->random(3) as $p) {
            $qty = [12, 24, 24, 48][mt_rand(0, 3)];
            $entry->lines()->create([
                'product_id' => $p->id,
                'name' => $p->name,
                'brand' => $p->brand->name,
                'category' => $p->category->name,
                'qty' => $qty,
                'purchase_rate' => mt_rand(0, 1) ? round(lineRate($p) * 0.92, 2) : null,
            ]);
            $p->increment('stock_qty', $qty);
        }
    }

    // ---------- Retailer orders (pending + cancelled) ----------
    $orderPartner = Partner::where('mobile', '9000000001')->first();
    foreach ([['pending', 'need before weekend please'], ['pending', null], ['cancelled', 'ordered by mistake']] as [$status, $note]) {
        $order = Order::create(['partner_id' => $orderPartner->id, 'status' => $status, 'note' => $note]);
        foreach ($products->random(2) as $p) {
            $order->lines()->create([
                'product_id' => $p->id,
                'name' => $p->name,
                'brand' => $p->brand->name,
                'category' => $p->category->name,
                'mrp' => $p->mrp,
                'qty' => [6, 12, 24][mt_rand(0, 2)],
            ]);
        }
    }

});

$counts = [
    'partners' => Partner::count(),
    'invoices' => Invoice::count(),
    'payments' => Payment::count(),
    'credit_notes' => CreditNote::count(),
    'suppliers' => Supplier::count(),
    'supplier_bills' => SupplierBill::count(),
    'supplier_payments' => SupplierPayment::count(),
    'inward_entries' => InwardEntry::count(),
    'orders' => Order::count(),
];

echo "Done. Current table counts:\n";
foreach ($counts as $k => $v) {
    echo str_pad($k, 20) . $v . "\n";
}
