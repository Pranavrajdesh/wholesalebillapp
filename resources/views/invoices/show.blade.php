<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice INV-{{ $invoice->invoice_no }}</title>
    @php
        $upiLink = null;
        if (!empty($s['upi_id'])) {
            $upiLink = 'upi://pay?pa=' . rawurlencode($s['upi_id'])
                . '&pn=' . rawurlencode($s['firm_name'] ?? 'Payment')
                . '&cu=INR&tn=' . rawurlencode('INV-' . $invoice->invoice_no);
        }
        $hasBank = !empty($s['bank_account']) && !empty($s['bank_ifsc']);
        $showPayment = ($s['print_payment'] ?? '1') === '1';
        $showProjection = ($s['print_projection'] ?? '1') === '1';
    @endphp
    @if ($upiLink)
        @vite(['resources/js/invoice.js'])
    @endif
    <style id="pagestyle">@page { size: A4 portrait; margin: 10mm; }</style>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, sans-serif; color: #111; background: #ececec; }
        .sheet { max-width: 800px; margin: 16px auto; background: #fff; padding: 28px 32px; border: 1px solid #ccc; }
        .topbar { max-width: 800px; margin: 12px auto 0; display: flex; gap: 8px; padding: 0 8px; flex-wrap: wrap; }
        .tbtn { flex: 1; min-width: 142px; padding: 11px; text-align: center; background: #1a1a1a; color: #fff; text-decoration: none; border: none; font-size: 13px; cursor: pointer; border-radius: 4px; }
        .tbtn.outline { background: #fff; color: #1a1a1a; border: 1px solid #1a1a1a; }
        .head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #111; padding-bottom: 12px; }
        .firm { font-size: 22px; font-weight: 700; }
        .firmmeta { font-size: 12px; color: #555; line-height: 1.5; }
        .doc { text-align: right; }
        .doc .no { font-size: 18px; font-weight: 700; }
        .parties { display: flex; justify-content: space-between; gap: 16px; margin: 14px 0; }
        .party { font-size: 13px; line-height: 1.5; }
        .party .label { font-size: 11px; font-weight: 700; color: #666; letter-spacing: 0.5px; }
        .party .pname { font-weight: 700; font-size: 15px; }
        .tablewrap { overflow-x: auto; }
        table.lines { width: 100%; border-collapse: collapse; font-size: 12.5px; margin-top: 6px; }
        table.lines th { text-align: left; border-top: 1px solid #111; border-bottom: 1px solid #111; padding: 6px 6px; font-size: 11.5px; }
        table.lines td { padding: 5px 6px; border-bottom: 1px solid #e5e5e5; vertical-align: top; }
        table.lines .num { text-align: right; white-space: nowrap; }
        tr.brandrow td { font-weight: 700; padding-top: 10px; border-bottom: 1px solid #bbb; }
        tr.catrow td { font-size: 10.5px; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: none; padding-bottom: 2px; }
        .schemenote { font-size: 10.5px; color: #666; }
        .linelist { display: none; margin-top: 8px; }
        .vbrand { font-weight: 700; font-size: 13px; margin: 12px 0 1px; border-bottom: 1px solid #bbb; padding-bottom: 2px; }
        .vcat { font-size: 10.5px; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin: 4px 0 4px; }
        .vline { border: 1px solid #1a1a1a; border-radius: 4px; padding: 8px 10px; margin: 8px 0; font-size: 12.5px; }
        .vname { font-weight: 600; }
        .vmeta { font-size: 11px; color: #666; }
        .vamt { display: flex; justify-content: space-between; margin-top: 2px; }
        .vamt b { white-space: nowrap; }
        .totals { margin-top: 14px; margin-left: auto; width: 280px; font-size: 13.5px; }
        .totals .row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #1a1a1a; }
        .totals .row:has(+ .row.grand) { border-bottom: none; }
        .totals .row.grand { border-bottom: none; }
        .totals .row.grand { border-top: 2px solid #111; margin-top: 4px; padding-top: 8px; font-size: 17px; font-weight: 700; }
        .dnote { font-size: 11.5px; color: #666; }
        .secttitle { font-size: 11px; font-weight: 700; color: #1a1a1a; letter-spacing: 0.6px; margin: 20px 0 4px; }
        table.mini { border: 1.5px solid #1a1a1a; }
        table.mini th { border-bottom: 1.5px solid #1a1a1a; }
        table.mini td { border-bottom: 1px solid #999; }
        table.mini tr.tot td { border-top: 1.5px solid #1a1a1a; border-bottom: none; }
        /* items table: dark frame + row partitions */

        /* mobile item cards: partitions between name / meta / amount rows */
        .vline .vmeta { border-top: 1px dashed #999; margin-top: 5px; padding-top: 5px; }
        .vline .vamt { border-top: 1px dashed #999; margin-top: 5px; padding-top: 5px; }
        table.mini { width: 100%; border-collapse: collapse; font-size: 12px; }
        table.mini th { text-align: left; border-top: 1px solid #999; border-bottom: 1px solid #999; padding: 5px 6px; font-size: 11px; }
        table.mini td { padding: 4px 6px; border-bottom: 1px solid #e5e5e5; }
        table.mini .num { text-align: right; white-space: nowrap; }
        table.mini tr.tot td { font-weight: 700; border-top: 1px solid #999; border-bottom: none; }
        .projection { margin-top: 18px; border: 1px solid #bbb; border-left: 4px solid #111; padding: 10px 12px; font-size: 13px; }
        .projection .big { font-size: 16px; font-weight: 700; }
        .projrow { display: flex; justify-content: space-between; gap: 10px; padding: 6px 0; border-bottom: 1px solid #999; }
        .projrow:last-child { border-bottom: none; }
        .projrow .pv { text-align: right; white-space: nowrap; font-weight: 700; }
        .projrow { display: flex; justify-content: space-between; gap: 10px; padding: 6px 0; border-bottom: 1px solid #999; }
        .projrow:last-child { border-bottom: none; }
        .projrow .pv { text-align: right; white-space: nowrap; font-weight: 700; }
        .paybox { margin-top: 18px; border: 1px solid #bbb; padding: 12px; display: flex; gap: 16px; justify-content: space-between; align-items: flex-start; }
        .paybox .bank { font-size: 13px; line-height: 1.6; }
        .paybox .qrwrap { text-align: center; font-size: 11px; color: #444; }
        .paybox .qrwrap canvas { display: block; margin: 0 auto 3px; }
        .foot { margin-top: 26px; display: flex; justify-content: space-between; font-size: 12px; color: #444; }
        .sign { margin-top: 34px; border-top: 1px solid #999; padding-top: 4px; width: 200px; text-align: center; }
        @media (max-width: 640px) {
            .sheet { margin: 8px; padding: 16px 12px; }
            .head { flex-direction: column; gap: 8px; }
            .doc { text-align: left; }
            .parties { flex-direction: column; }
            .tablewrap { display: none; }
            .linelist { display: block; }
            .totals { width: 100%; }
            .paybox { flex-direction: column; align-items: center; }
        }
        @media print {
            .noprint { display: none !important; }
            body { background: #fff; }
            .sheet { max-width: none; margin: 0; border: none; padding: 0; }
            .topbar { display: none; }
        }
        @media print {
            body.fmt-a4 .tablewrap, body.fmt-a5 .tablewrap { display: block; overflow: visible; }
            body.fmt-a4 .linelist, body.fmt-a5 .linelist { display: none; }
            body.fmt-a5 .sheet { font-size: 11px; }
            body.fmt-a5 table.lines { font-size: 10.5px; }
            body.fmt-a5 table.lines th { font-size: 9.5px; padding: 4px 4px; }
            body.fmt-a5 table.lines td { padding: 3px 4px; }
            body.fmt-a5 .firm { font-size: 17px; }
            body.fmt-a5 .totals { font-size: 11.5px; width: 240px; }
            body.fmt-a5 .totals .row.grand { font-size: 14px; }
            body.fmt-a5 table.mini { font-size: 10px; }
            body.fmt-a5 .projection { font-size: 11px; }
            body.fmt-a5 .projection .big { font-size: 13px; }
            body.fmt-a5 .paybox { font-size: 11px; }
        }
        @media print {
            body.fmt-thermal .sheet { width: 72mm; margin: 0 auto; }
            body.fmt-thermal .tablewrap { display: none; }
            body.fmt-thermal .linelist { display: block; }
            body.fmt-thermal .head { flex-direction: column; gap: 2px; border-bottom: 1px solid #111; padding-bottom: 6px; text-align: center; }
            body.fmt-thermal .firm { font-size: 15px; }
            body.fmt-thermal .firmmeta { font-size: 9.5px; }
            body.fmt-thermal .doc { text-align: center; }
            body.fmt-thermal .doc .no { font-size: 14px; }
            body.fmt-thermal .parties { margin: 8px 0; }
            body.fmt-thermal .party { font-size: 11px; line-height: 1.35; }
            body.fmt-thermal .party .pname { font-size: 12.5px; }
            body.fmt-thermal .vline { font-size: 11px; padding: 3px 0; border: none; border-bottom: 1px dashed #ccc; border-radius: 0; margin: 0; }
            body.fmt-thermal .vmeta { font-size: 9.5px; }
            body.fmt-thermal .vbrand { font-size: 11px; margin-top: 8px; }
            body.fmt-thermal .vcat { font-size: 9px; }
            body.fmt-thermal .totals { width: 100%; font-size: 11.5px; margin-top: 8px; }
            body.fmt-thermal .totals .row.grand { font-size: 14px; }
            body.fmt-thermal .secttitle { margin: 10px 0 2px; font-size: 9.5px; }
            body.fmt-thermal table.mini { font-size: 9px; }
            body.fmt-thermal table.mini th { font-size: 8.5px; padding: 3px 3px; }
            body.fmt-thermal table.mini td { padding: 2px 3px; }
            body.fmt-thermal .projection { font-size: 10px; padding: 6px 8px; margin-top: 10px; }
            body.fmt-thermal .projection .big { font-size: 11.5px; }
            body.fmt-thermal .paybox { flex-direction: column; align-items: center; font-size: 10px; padding: 8px; margin-top: 10px; }
            body.fmt-thermal .foot { margin-top: 10px; font-size: 9.5px; flex-direction: column; gap: 4px; }
            body.fmt-thermal .sign { display: none; }
        }

        /* ---- borders & alignment ---- */
        table.lines th, table.lines td { border: 1px solid #1a1a1a; }
        tr.brandrow td { background: #f2f2f2; }
        @media print {
            body.fmt-a4 .sheet, body.fmt-a5 .sheet { border: 1.5px solid #111; padding: 5mm; }
            body.fmt-a4 table.lines th, body.fmt-a4 table.lines td,
            body.fmt-a5 table.lines th, body.fmt-a5 table.lines td { border-color: #1a1a1a; }
            body.fmt-thermal .head > div { width: 100%; text-align: center; }
        }

        /* ---- share modal ---- */
        .smodal { position: fixed; inset: 0; background: rgba(0,0,0,0.55); display: flex; align-items: center; justify-content: center; padding: 16px; z-index: 50; }
        .smodal[hidden] { display: none; }
        .smodal .sbox { background: #fff; border-radius: 6px; width: 100%; max-width: 380px; padding: 16px; }
        .smodal .stitle { font-weight: 700; font-size: 16px; display: flex; justify-content: space-between; align-items: center; }
        .smodal .sx { background: none; border: none; font-size: 24px; cursor: pointer; line-height: 1; }
        .smodal label { display: block; font-size: 13px; margin: 12px 0 4px; font-weight: 600; }
        .smodal input { width: 100%; padding: 10px; font-size: 15px; border: 1px solid #aaa; border-radius: 4px; }
        .smodal .err { color: #b00020; font-size: 12.5px; margin-top: 6px; display: none; }
        .smodal .sgo { width: 100%; margin-top: 14px; padding: 12px; background: #1a1a1a; color: #fff; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; }
    </style>
</head>
<body class="fmt-a4">
    <div class="topbar">
        @if (!$public)
            <a class="tbtn outline" href="{{ route('products.index') }}">HOME</a>
            <a class="tbtn outline" href="{{ route('billing.select') }}">NEW BILL</a>
            <button type="button" class="tbtn" onclick="openActions()">&#9776; MORE ACTIONS</button>
        @else
            <button type="button" class="tbtn" onclick="printAs('a4')">PRINT A4</button>
            <a class="tbtn" href="{{ $pdfUrl }}">DOWNLOAD PDF</a>
        @endif
    </div>

    @if (!$public)
        <div id="actionsmodal" class="smodal" hidden>
            <div class="sbox">
                <div class="stitle">
                    <span>Invoice Actions</span>
                    <button type="button" class="sx" onclick="closeActions()">&times;</button>
                </div>
                <div style="display:flex; flex-direction:column; gap:8px; margin-top:14px;">
                    <button type="button" class="tbtn" onclick="closeActions(); printAs('a4')">PRINT A4</button>
                    <button type="button" class="tbtn" onclick="closeActions(); printAs('a5')">PRINT A5</button>
                    <button type="button" class="tbtn" onclick="closeActions(); printAs('thermal')">PRINT 3&quot;</button>
                    <a class="tbtn" href="{{ $pdfUrl }}">DOWNLOAD PDF</a>
                    <button type="button" class="tbtn" onclick="closeActions(); openShare()">SHARE ON WHATSAPP</button>
                    <button type="button" class="tbtn" id="copylink" onclick="copyLink()">COPY LINK</button>
                    <a class="tbtn" href="{{ route('ledger.show', $invoice->partner) }}">PARTNER LEDGER</a>
                </div>
            </div>
        </div>
    @endif

    <div class="sheet">
        <div class="head">
            <div>
                <div class="firm">{{ $s['firm_name'] ?? (auth()->user()->name ?? 'wholesaleBillApp') }}</div>
                <div class="firmmeta">
                    @if (!empty($s['firm_gst'])) GSTIN: {{ $s['firm_gst'] }}<br>@endif
                    Mobile: {{ $s['firm_mobile'] ?? (auth()->user()->mobile ?? '') }}@if (!empty($s['firm_alt_mobile'])), {{ $s['firm_alt_mobile'] }}@endif
                    @if (!empty($s['firm_address']))<br>{{ $s['firm_address'] }}@endif
                </div>
            </div>
            <div class="doc">
                <div style="font-size:12px; letter-spacing:1px; color:#555;">INVOICE</div>
                <div class="no">INV-{{ $invoice->invoice_no }}</div>
                <div style="font-size:13px;">{{ $invoice->invoice_date->format('d M Y') }}</div>
            </div>
        </div>

        <div class="parties">
            <div class="party">
                <div class="label">BILL TO</div>
                <div class="pname">{{ $invoice->partner->firm_name }}</div>
                @if ($invoice->partner->contact_name)
                    <div>{{ $invoice->partner->contact_name }}</div>
                @endif
                <div>Mobile: {{ $invoice->partner->mobile }}@if ($invoice->partner->alt_mobile), {{ $invoice->partner->alt_mobile }}@endif</div>
                @if ($invoice->partner->gst_number)
                    <div>GSTIN: {{ $invoice->partner->gst_number }}</div>
                @endif
                @if ($invoice->partner->address)
                    <div>{{ $invoice->partner->address }}</div>
                @endif
            </div>
        </div>

        <div class="tablewrap">
            <table class="lines">
                <thead>
                    <tr>
                        <th style="width:26px;">#</th>
                        <th>Item</th>
                        <th style="width:56px;">HSN</th>
                        <th class="num" style="width:64px;">MRP</th>
                        <th class="num" style="width:44px;">Qty</th>
                        <th class="num" style="width:44px;">Free</th>
                        <th class="num" style="width:70px;">Rate</th>
                        <th class="num" style="width:84px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sn = 0; $curBrand = null; $curCat = null; @endphp
                    @foreach ($invoice->lines as $line)
                        @if ($line->brand !== $curBrand)
                            @php $curBrand = $line->brand; $curCat = null; @endphp
                            <tr class="brandrow"><td colspan="8">{{ $line->brand }}</td></tr>
                        @endif
                        @if ($line->category !== $curCat)
                            @php $curCat = $line->category; @endphp
                            <tr class="catrow"><td colspan="8" style="font-weight:700; color:#1a1a1a;">{{ $line->category }}</td></tr>
                        @endif
                        @php $sn++; @endphp
                        <tr>
                            <td>{{ $sn }}</td>
                            <td>
                                {{ $line->name }}
                                @if ($line->scheme_percent > 0)
                                    <div class="schemenote" style="font-weight:700; color:#1a1a1a;">Scheme {{ rtrim(rtrim(number_format($line->scheme_percent, 2), '0'), '.') }}%</div>
                                @endif
                            </td>
                            <td>{{ $line->hsn_code }}</td>
                            <td class="num">{{ number_format($line->mrp, 2) }}</td>
                            <td class="num">{{ $line->qty }}</td>
                            <td class="num">{{ $line->free_qty > 0 ? $line->free_qty : '' }}</td>
                            <td class="num">{{ number_format($line->rate, 2) }}@if (!$line->tax_inclusive && $line->tax_percent > 0)<div style="font-size:9.5px; font-weight:700;">+{{ rtrim(rtrim(number_format($line->tax_percent, 2), '0'), '.') }}% GST</div>@endif</td>
                            <td class="num">{{ number_format($line->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="linelist">
            @php $vn = 0; $curBrand = null; $curCat = null; @endphp
            @foreach ($invoice->lines as $line)
                @if ($line->brand !== $curBrand)
                    @php $curBrand = $line->brand; $curCat = null; @endphp
                    <div class="vbrand">{{ $line->brand }}</div>
                @endif
                @if ($line->category !== $curCat)
                    @php $curCat = $line->category; @endphp
                    <div class="vcat" style="font-weight:700; color:#1a1a1a;">{{ $line->category }}</div>
                @endif
                <div class="vline">
                    <div class="vname">{{ ++$vn }}. {{ $line->name }}</div>
                    <div class="vmeta" style="font-weight:700; color:#1a1a1a;">
                        @if ($line->hsn_code) HSN {{ $line->hsn_code }} &middot; @endif
                        MRP {{ number_format($line->mrp, 2) }}
                        @if ($line->scheme_percent > 0) &middot; Sch {{ rtrim(rtrim(number_format($line->scheme_percent, 2), '0'), '.') }}% @endif
                    </div>
                    <div class="vamt">
                        <span>{{ $line->qty }} &times; {{ number_format($line->rate, 2) }}@if (!$line->tax_inclusive && $line->tax_percent > 0) <b style="font-size:11px;">+{{ rtrim(rtrim(number_format($line->tax_percent, 2), '0'), '.') }}% GST</b>@endif @if ($line->free_qty > 0) (+{{ $line->free_qty }} free)@endif</span>
                        <b>{{ number_format($line->amount, 2) }}</b>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="totals">
            <div class="row"><span>Subtotal</span><span>Rs {{ number_format($invoice->subtotal, 2) }}</span></div>
            @if ($invoice->discount_amount > 0)
                <div class="row">
                    <span>Discount{{ $invoice->discount_type === 'percent' ? ' (' . rtrim(rtrim(number_format($invoice->discount_value, 2), '0'), '.') . '%)' : '' }}</span>
                    <span>- Rs {{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
                @if ($invoice->discount_note)
                    <div class="dnote">{{ $invoice->discount_note }}</div>
                @endif
            @endif
            @if (abs($invoice->round_off) >= 0.01)
                <div class="row"><span>Round off</span><span>{{ $invoice->round_off >= 0 ? '+' : '-' }} Rs {{ number_format(abs($invoice->round_off), 2) }}</span></div>
            @endif
            <div class="row grand"><span>TOTAL</span><span>Rs {{ number_format($invoice->total, 2) }}</span></div>
        </div>

        @php
            $taxGroups = [];
            $hasTax = false;
            foreach ($invoice->lines as $line) {
                $key = ($line->hsn_code ?: '-') . '|' . $line->tax_percent;
                if (!isset($taxGroups[$key])) {
                    $taxGroups[$key] = ['hsn' => $line->hsn_code ?: '-', 'pct' => (float) $line->tax_percent, 'taxable' => 0.0, 'tax' => 0.0];
                }
                $pct = (float) $line->tax_percent;
                if ($pct > 0) {
                    $hasTax = true;
                    $taxable = $line->tax_inclusive ? $line->amount / (1 + $pct / 100) : (float) $line->amount;
                    $taxGroups[$key]['taxable'] += $taxable;
                    $taxGroups[$key]['tax'] += $taxable * $pct / 100;
                } else {
                    $taxGroups[$key]['taxable'] += (float) $line->amount;
                }
            }
            ksort($taxGroups);
            $sumTaxable = collect($taxGroups)->sum('taxable');
            $sumTax = collect($taxGroups)->sum('tax');

            $mrpValue = $invoice->lines->sum(fn ($l) => $l->mrp * ($l->qty + $l->free_qty));
            $cost = (float) $invoice->total;
            $profit = $mrpValue - $cost;
        @endphp

        <div style="margin-top:14px; font-weight:700; color:#1a1a1a; font-size:13px;">
            Total items: {{ $invoice->lines->count() }} &middot;
            Qty: {{ $invoice->lines->sum('qty') }}@if ($invoice->lines->sum('free_qty') > 0) + {{ $invoice->lines->sum('free_qty') }} free = {{ $invoice->lines->sum('qty') + $invoice->lines->sum('free_qty') }}@endif
        </div>

        @if ($hasTax)
            <div class="secttitle">TAX SUMMARY (HSN-WISE)</div>
            <table class="mini">
                <thead>
                    <tr>
                        <th>HSN</th>
                        <th class="num">GST %</th>
                        <th class="num">Taxable</th>
                        <th class="num">CGST</th>
                        <th class="num">SGST</th>
                        <th class="num">Tax</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($taxGroups as $g)
                        <tr>
                            <td>{{ $g['hsn'] }}</td>
                            <td class="num">{{ rtrim(rtrim(number_format($g['pct'], 2), '0'), '.') }}%</td>
                            <td class="num">{{ number_format($g['taxable'], 2) }}</td>
                            <td class="num">{{ number_format($g['tax'] / 2, 2) }}</td>
                            <td class="num">{{ number_format($g['tax'] / 2, 2) }}</td>
                            <td class="num">{{ number_format($g['tax'], 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="tot">
                        <td colspan="2">Total</td>
                        <td class="num">{{ number_format($sumTaxable, 2) }}</td>
                        <td class="num">{{ number_format($sumTax / 2, 2) }}</td>
                        <td class="num">{{ number_format($sumTax / 2, 2) }}</td>
                        <td class="num">{{ number_format($sumTax, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="dnote" style="margin-top:3px;">@if ($invoice->lines->contains(fn ($l) => !$l->tax_inclusive && $l->tax_percent > 0))Rates marked &ldquo;+GST&rdquo; have tax added on top; all other rates are tax inclusive.@else Tax amounts are included in the item rates (inclusive pricing).@endif</div>
        @endif

        @if ($mrpValue > 0 && $cost > 0)
            <div class="projection {{ $showProjection ? '' : 'noprint' }}">
                <div style="font-size:11px; font-weight:700; color:#666; letter-spacing:0.6px;">YOUR PROJECTED PROFIT (AT MRP)</div>
                <div style="margin-top:4px;">
                    <div class="projrow"><span>Stock value at MRP</span><span class="pv">Rs {{ number_format($mrpValue, 2) }}</span></div>
                    <div class="projrow"><span>Units (incl. free)</span><span class="pv">{{ $invoice->lines->sum('qty') + $invoice->lines->sum('free_qty') }}</span></div>
                    <div class="projrow"><span>This bill</span><span class="pv">Rs {{ number_format($cost, 2) }}</span></div>
                    <div class="projrow big"><span>Profit ({{ number_format($profit / $cost * 100, 1) }}% on cost)</span><span class="pv">Rs {{ number_format($profit, 2) }}</span></div>
                    <div class="projrow"><span>Cost margin (MRP value &divide; bill)</span><span class="pv">{{ number_format($mrpValue / $cost, 2) }}</span></div>
                </div>
            </div>
        @endif

        @if ($hasBank || $upiLink)
            <div class="paybox {{ $showPayment ? '' : 'noprint' }}">
                @if ($hasBank)
                    <div class="bank">
                        <div style="font-size:11px; font-weight:700; color:#666; letter-spacing:0.6px;">PAYMENT DETAILS</div>
                        @if (!empty($s['bank_holder']))<div>{{ $s['bank_holder'] }}</div>@endif
                        @if (!empty($s['bank_name']))<div>{{ $s['bank_name'] }}</div>@endif
                        <div>A/c: <b>{{ $s['bank_account'] }}</b></div>
                        <div>IFSC: <b>{{ $s['bank_ifsc'] }}</b></div>
                        @if (!empty($s['upi_id']))<div>UPI: {{ $s['upi_id'] }}</div>@endif
                    </div>
                @endif
                @if ($upiLink)
                    <div class="qrwrap">
                        <div id="upiqr" data-upi="{{ $upiLink }}"></div>
                        Scan &amp; pay via UPI<br>Bill total: <b>Rs {{ number_format($invoice->total, 2) }}</b>
                    </div>
                @endif
            </div>
        @endif


    </div>

    @if (!$public)
        <div id="sharemodal" class="smodal" hidden>
            <div class="sbox">
                <div class="stitle">
                    <span>Send invoice on WhatsApp</span>
                    <button type="button" class="sx" onclick="closeShare()">&times;</button>
                </div>
                <label for="wanum">Receiver mobile number</label>
                <input type="tel" id="wanum" inputmode="numeric" maxlength="10" value="{{ $invoice->partner->mobile }}">
                <div class="err" id="waerr">Enter a valid 10-digit mobile number.</div>
                <button type="button" class="sgo" onclick="sendWhatsApp()">OPEN WHATSAPP</button>
            </div>
        </div>
    @endif

    <script>
        @if (!$public)
        const WA_TEXT = @json($waText);
        const PUBLIC_URL = @json($publicUrl);

        function openActions() { document.getElementById('actionsmodal').hidden = false; }
        function closeActions() { document.getElementById('actionsmodal').hidden = true; }
        document.getElementById('actionsmodal') && document.getElementById('actionsmodal').addEventListener('click', e => { if (e.target.id === 'actionsmodal') closeActions(); });

        function openShare() { document.getElementById('sharemodal').hidden = false; document.getElementById('wanum').focus(); }
        function closeShare() { document.getElementById('sharemodal').hidden = true; }

        function sendWhatsApp() {
            const num = document.getElementById('wanum').value.trim();
            const err = document.getElementById('waerr');
            if (!/^\d{10}$/.test(num)) { err.style.display = 'block'; return; }
            err.style.display = 'none';
            closeShare();
            window.open('https://wa.me/91' + num + '?text=' + encodeURIComponent(WA_TEXT), '_blank');
        }

        function copyLink() {
            const done = () => {
                const b = document.getElementById('copylink');
                const t = b.textContent;
                b.textContent = 'COPIED \u2713';
                setTimeout(() => { b.textContent = t; }, 1400);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(PUBLIC_URL).then(done);
            } else {
                const ta = document.createElement('textarea');
                ta.value = PUBLIC_URL;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                done();
            }
        }

        document.getElementById('sharemodal').addEventListener('click', e => { if (e.target.id === 'sharemodal') closeShare(); });
        document.getElementById('wanum').addEventListener('keydown', e => { if (e.key === 'Enter') sendWhatsApp(); });
        @endif

        function printAs(fmt) {
            document.body.className = 'fmt-' + fmt;
            document.getElementById('pagestyle').textContent =
                fmt === 'a5' ? '@page { size: A5 portrait; margin: 8mm; }'
                : fmt === 'thermal' ? '@page { size: 80mm auto; margin: 3mm; }'
                : '@page { size: A4 portrait; margin: 10mm; }';
            window.print();
        }
    </script>
</body>
</html>
