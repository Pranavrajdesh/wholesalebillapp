<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice INV-{{ $invoice->invoice_no }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        .wrap { border: 2px solid #111; padding: 14px 16px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        .headtbl td { border-bottom: 2px solid #111; padding-bottom: 8px; }
        .firm { font-size: 17px; font-weight: bold; }
        .firmmeta { font-size: 9px; color: #444; line-height: 1.5; margin-top: 2px; }
        .doctitle { font-size: 9px; letter-spacing: 2px; color: #555; text-align: right; }
        .docno { font-size: 14px; font-weight: bold; text-align: right; }
        .docdate { font-size: 10px; text-align: right; }
        .partytbl { margin-top: 8px; margin-bottom: 8px; }
        .plabel { font-size: 8px; font-weight: bold; color: #666; letter-spacing: 1px; }
        .pname { font-size: 12px; font-weight: bold; }
        .pmeta { font-size: 9px; line-height: 1.5; }
        table.lines th { background: #f2f2f2; border: 1px solid #888; border-top: 1px solid #111; border-bottom: 1px solid #111; padding: 4px 5px; font-size: 8.5px; text-align: left; }
        table.lines td { border: 1px solid #bbb; padding: 3px 5px; font-size: 9.5px; }
        table.lines .num { text-align: right; white-space: nowrap; }
        table.lines .brandrow td { background: #ececec; font-weight: bold; font-size: 9.5px; }
        table.lines .catrow td { font-size: 8px; font-weight: bold; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
        .schemenote { font-size: 8px; color: #666; }
        .totwrap { margin-top: 8px; }
        table.tot { width: 240px; }
        table.tot td { padding: 2px 0; font-size: 10px; }
        table.tot td.v { text-align: right; }
        table.tot tr.grand td { border-top: 2px solid #111; padding-top: 5px; font-size: 13px; font-weight: bold; }
        .dnote { font-size: 8.5px; color: #666; }
        .secttitle { font-size: 8.5px; font-weight: bold; color: #666; letter-spacing: 1px; margin: 12px 0 3px; }
        table.mini th { border-top: 1px solid #999; border-bottom: 1px solid #999; padding: 3px 5px; font-size: 8px; text-align: left; }
        table.mini td { border-bottom: 1px solid #ddd; padding: 3px 5px; font-size: 9px; }
        table.mini .num { text-align: right; }
        table.mini tr.tot2 td { font-weight: bold; border-top: 1px solid #999; border-bottom: none; }
        .projection { margin-top: 12px; border: 1px solid #999; border-left: 4px solid #111; padding: 7px 9px; font-size: 9.5px; }
        .projection .big { font-size: 11.5px; font-weight: bold; margin-top: 3px; }
        .paytbl { margin-top: 12px; border: 1px solid #999; }
        .paytbl td { padding: 8px 10px; font-size: 9.5px; line-height: 1.6; }
        .paytbl .qrcell { width: 130px; text-align: center; font-size: 8.5px; color: #444; }
        .foot { margin-top: 14px; font-size: 9px; color: #444; }
    </style>
</head>
<body>
<div class="wrap">

    <table class="headtbl">
        <tr>
            <td>
                <div class="firm">{{ $s['firm_name'] ?? 'Invoice' }}</div>
                <div class="firmmeta">
                    @if (!empty($s['firm_gst'])) GSTIN: {{ $s['firm_gst'] }}<br>@endif
                    Mobile: {{ $s['firm_mobile'] ?? '' }}@if (!empty($s['firm_alt_mobile'])), {{ $s['firm_alt_mobile'] }}@endif
                    @if (!empty($s['firm_address']))<br>{{ $s['firm_address'] }}@endif
                </div>
            </td>
            <td style="width:170px;">
                <div class="doctitle">INVOICE</div>
                <div class="docno">INV-{{ $invoice->invoice_no }}</div>
                <div class="docdate">{{ $invoice->invoice_date->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="partytbl">
        <tr>
            <td>
                <div class="plabel">BILL TO</div>
                <div class="pname">{{ $invoice->partner->firm_name }}</div>
                <div class="pmeta">
                    @if ($invoice->partner->contact_name){{ $invoice->partner->contact_name }}<br>@endif
                    Mobile: {{ $invoice->partner->mobile }}@if ($invoice->partner->alt_mobile), {{ $invoice->partner->alt_mobile }}@endif
                    @if ($invoice->partner->gst_number)<br>GSTIN: {{ $invoice->partner->gst_number }}@endif
                    @if ($invoice->partner->address)<br>{{ $invoice->partner->address }}@endif
                </div>
            </td>
        </tr>
    </table>

    <table class="lines">
        <thead>
            <tr>
                <th style="width:22px;">#</th>
                <th>Item</th>
                <th style="width:50px;">HSN</th>
                <th class="num" style="width:56px;">MRP</th>
                <th class="num" style="width:38px;">Qty</th>
                <th class="num" style="width:38px;">Free</th>
                <th class="num" style="width:60px;">Rate</th>
                <th class="num" style="width:74px;">Amount</th>
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
                    <tr class="catrow"><td colspan="8">{{ $line->category }}</td></tr>
                @endif
                @php $sn++; @endphp
                <tr>
                    <td>{{ $sn }}</td>
                    <td>
                        {{ $line->name }}
                        @if ($line->scheme_percent > 0)
                            <div class="schemenote">Scheme {{ rtrim(rtrim(number_format($line->scheme_percent, 2), '0'), '.') }}%</div>
                        @endif
                    </td>
                    <td>{{ $line->hsn_code }}</td>
                    <td class="num">{{ number_format($line->mrp, 2) }}</td>
                    <td class="num">{{ $line->qty }}</td>
                    <td class="num">{{ $line->free_qty > 0 ? $line->free_qty : '' }}</td>
                    <td class="num">{{ number_format($line->rate, 2) }}@if (!$line->tax_inclusive && $line->tax_percent > 0)<div style="font-size:7px; font-weight:bold;">+{{ rtrim(rtrim(number_format($line->tax_percent, 2), '0'), '.') }}% GST</div>@endif</td>
                    <td class="num">{{ number_format($line->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totwrap">
        <table style="width:100%;">
            <tr>
                <td></td>
                <td style="width:240px;">
                    <table class="tot">
                        <tr><td>Subtotal</td><td class="v">Rs {{ number_format($invoice->subtotal, 2) }}</td></tr>
                        @if ($invoice->discount_amount > 0)
                            <tr>
                                <td>Discount{{ $invoice->discount_type === 'percent' ? ' (' . rtrim(rtrim(number_format($invoice->discount_value, 2), '0'), '.') . '%)' : '' }}</td>
                                <td class="v">- Rs {{ number_format($invoice->discount_amount, 2) }}</td>
                            </tr>
                            @if ($invoice->discount_note)
                                <tr><td colspan="2" class="dnote">{{ $invoice->discount_note }}</td></tr>
                            @endif
                        @endif
                        @if (abs($invoice->round_off) >= 0.01)
                            <tr><td>Round off</td><td class="v">{{ $invoice->round_off >= 0 ? '+' : '-' }} Rs {{ number_format(abs($invoice->round_off), 2) }}</td></tr>
                        @endif
                        <tr class="grand"><td>TOTAL</td><td class="v">Rs {{ number_format($invoice->total, 2) }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
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

        $hasBank = !empty($s['bank_account']) && !empty($s['bank_ifsc']);
    @endphp

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
                <tr class="tot2">
                    <td colspan="2">Total</td>
                    <td class="num">{{ number_format($sumTaxable, 2) }}</td>
                    <td class="num">{{ number_format($sumTax / 2, 2) }}</td>
                    <td class="num">{{ number_format($sumTax / 2, 2) }}</td>
                    <td class="num">{{ number_format($sumTax, 2) }}</td>
                </tr>
            </tbody>
        </table>
        <div class="dnote" style="margin-top:2px;">@if ($invoice->lines->contains(fn ($l) => !$l->tax_inclusive && $l->tax_percent > 0))Rates marked &ldquo;+GST&rdquo; have tax added on top; all other rates are tax inclusive.@else Tax amounts are included in the item rates (inclusive pricing).@endif</div>
    @endif

    @if ($mrpValue > 0 && $cost > 0)
        <div class="projection">
            <div style="font-size:8.5px; font-weight:bold; color:#666; letter-spacing:1px;">YOUR PROJECTED PROFIT (AT MRP)</div>
            <div style="margin-top:4px;">
                Stock value at MRP: <b>Rs {{ number_format($mrpValue, 2) }}</b>
                ({{ $invoice->lines->sum('qty') + $invoice->lines->sum('free_qty') }} units incl. free)
                &nbsp;&middot;&nbsp; This bill: <b>Rs {{ number_format($cost, 2) }}</b>
            </div>
            <div class="big">
                Profit: Rs {{ number_format($profit, 2) }}
                ({{ number_format($profit / $cost * 100, 1) }}% on cost &middot; margin {{ number_format($mrpValue / $cost, 2) }})
            </div>
        </div>
    @endif

    @if ($hasBank || $qrDataUri)
        <table class="paytbl">
            <tr>
                <td>
                    <div style="font-size:8.5px; font-weight:bold; color:#666; letter-spacing:1px;">PAYMENT DETAILS</div>
                    @if ($hasBank)
                        @if (!empty($s['bank_holder'])){{ $s['bank_holder'] }}<br>@endif
                        @if (!empty($s['bank_name'])){{ $s['bank_name'] }}<br>@endif
                        A/c: <b>{{ $s['bank_account'] }}</b> &nbsp;|&nbsp; IFSC: <b>{{ $s['bank_ifsc'] }}</b><br>
                    @endif
                    @if (!empty($s['upi_id']))UPI: {{ $s['upi_id'] }}@endif
                </td>
                @if ($qrDataUri)
                    <td class="qrcell">
                        <img src="{{ $qrDataUri }}" width="105" height="105"><br>
                        Scan &amp; pay via UPI<br>
                        Bill total: <b>Rs {{ number_format($invoice->total, 2) }}</b>
                    </td>
                @endif
            </tr>
        </table>
    @endif

    <div class="foot">
        Total items: {{ $invoice->lines->count() }} &middot;
        Qty: {{ $invoice->lines->sum('qty') }}@if ($invoice->lines->sum('free_qty') > 0) + {{ $invoice->lines->sum('free_qty') }} free @endif
    </div>

</div>
</body>
</html>
