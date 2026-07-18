<?php

// Run from the project root: php seed_rate_slabs.php
// Idempotent: products that already have ANY slab are skipped.

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$groupId = DB::table('rate_groups')->orderBy('id')->value('id');
if (!$groupId) {
    echo "No rate group found. Aborting.\n";
    exit(1);
}

$products = DB::table('products')->where('is_active', true)->get(['id', 'name', 'mrp']);
if ($products->isEmpty()) {
    echo "No active products. Aborting.\n";
    exit(1);
}

$withSlabs = DB::table('rate_slabs')->pluck('product_id')->unique()->flip();

$created = 0;
$skipped = 0;
$now = now();

foreach ($products as $p) {
    if (isset($withSlabs[$p->id])) {
        $skipped++;
        continue;
    }

    $mrp = (float) $p->mrp;
    if ($mrp <= 0) {
        $skipped++;
        continue;
    }

    // Deterministic per-product variation from the id
    $seed = $p->id;
    $baseMarginPct = 18 + ($seed % 8);          // tier-1 discount off MRP: 18-25%
    $step2 = 2 + ($seed % 3);                   // extra % off at tier 2: 2-4
    $step3 = 4 + ($seed % 4);                   // extra % off at tier 3: 4-7
    $tier2Qty = [6, 6, 10, 12][$seed % 4];
    $tier3Qty = $tier2Qty * [2, 3][$seed % 2];

    $r = fn (float $pct) => round($mrp * (1 - $pct / 100) * 2) / 2;  // to nearest 0.50

    $rows = [
        [
            'min_qty' => 1,
            'rate' => $r($baseMarginPct),
            'scheme_percent' => 0,
            'offer_buy_qty' => null,
            'offer_free_qty' => null,
        ],
        [
            'min_qty' => $tier2Qty,
            'rate' => $r($baseMarginPct + $step2),
            'scheme_percent' => ($seed % 3 === 0) ? 2.00 : 0,
            'offer_buy_qty' => null,
            'offer_free_qty' => null,
        ],
        [
            'min_qty' => $tier3Qty,
            'rate' => $r($baseMarginPct + $step2 + $step3),
            'scheme_percent' => ($seed % 3 === 0) ? 5.00 : (($seed % 3 === 1) ? 2.00 : 0),
            'offer_buy_qty' => ($seed % 4 === 0) ? $tier3Qty : null,
            'offer_free_qty' => ($seed % 4 === 0) ? 1 : null,
        ],
    ];

    foreach ($rows as $row) {
        DB::table('rate_slabs')->insert(array_merge($row, [
            'product_id' => $p->id,
            'rate_group_id' => $groupId,
            'created_at' => $now,
            'updated_at' => $now,
        ]));
    }
    $created++;
}

echo "Slabs created for {$created} products; {$skipped} skipped (already had slabs or zero MRP).\n";
