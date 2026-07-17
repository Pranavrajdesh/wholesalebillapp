<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Credit Note CN-{{ $cn->cn_no }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        .wrap { border: 2px solid #111; padding: 14px 16px; }
        table { width: 100%; border-collapse: collapse; }
        .headtbl td { border-bottom: 2px solid #111; padding-bottom: 8px; vertical-align: top; }
        .firm { font-size: 17px; font-weight: bold; }
        .firmmeta { font-size: 9px; color: #444; line-height: 1.5; margin-top: 2px; }
        .doctitle { font-size: 9px; letter-spacing: 2px; color: #555; text-align: right; }
        .docno { font-size: 14px; font-weight: bold; text-align: right; }
        .docdate { font-size: 10px; text-align: right; }
        .party { margin: 10px 0; font-size: 9.5px; line-height: 1.5; }
        .plabel { font-size: 8px; font-weight: bold; color: #666; letter-spacing: 1px; }
        .pname { font-size: 12px; font-weight: bold; }
        .reason { margin: 8px 0; font-size: 10px; font-weight: bold; padding: 7px 9px; border: 1px solid #999; border-left: 4px solid #111; background: #f7f7f7; }
        table.lines th { background: #f2f2f2; border: 1px solid #888; border-top: 1px solid #111; border-bottom: 1px solid #111; padding: 4px 5px; font-size: 8.5px; text-align: left; }
        table.lines td { border: 1px solid #bbb; padding: 3px 5px; font-size: 9.5px; }
        table.lines .num { text-align: right; white-space: nowrap; }
        table.lines .brandrow td { background: #ececec; font-weight: bold; }
        .totwrap { margin-top: 10px; }
        .grand { border-top: 2px solid #111; padding-top: 6px; font-size: 13px; font-weight: bold; }
        .creditmsg { margin-top: 12px; font-size: 10px; font-weight: bold; padding: 8px 10px; border: 1px solid #1e7e34; border-left: 4px solid #1e7e34; background: #e6f4ea; color: #1e7e34; }
        .foot { margin-top: 12px; font-size: 9px; color: #444; }
    </style>
</head>
<body>
<div class="wrap">

    <table class="headtbl">
        <tr>
            <td>
                <div class="firm">{{ $s['firm_name'] ?? 'Credit Note' }}</div>
                <div class="firmmeta">
                    @if (!empty($s['firm_gst'])) GSTIN: {{ $s['firm_gst'] }}<br>@endif
                    Mobile: {{ $s['firm_mobile'] ?? '' }}@if (!empty($s['firm_alt_mobile'])), {{ $s['firm_alt_mobile'] }}@endif
                    @if (!empty($s['firm_address']))<br>{{ $s['firm_address'] }}@endif
                </div>
            </td>
            <td style="width:170px;">
                <div class="doctitle">CREDIT NOTE</div>
                <div class="docno">CN-{{ $cn->cn_no }}</div>
                <div class="docdate">{{ $cn->cn_date->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="party">
        <div class="plabel">CREDITED TO</div>
        <div class="pname">{{ $cn->partner->firm_name }}</div>
        Mobile: {{ $cn->partner->mobile }}
        @if ($cn->partner->gst_number)<br>GSTIN: {{ $cn->partner->gst_number }}@endif
    </div>

    @if ($cn->reason)
        <div class="reason">Reason: {{ $cn->reason }}</div>
    @endif

    @if ($cn->kind === 'goods')
        <table class="lines">
            <thead>
                <tr>
                    <th style="width:22px;">#</th>
                    <th>Returned item</th>
                    <th class="num" style="width:56px;">MRP</th>
                    <th class="num" style="width:38px;">Qty</th>
                    <th class="num" style="width:60px;">Rate</th>
                    <th class="num" style="width:74px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $sn = 0; $curBrand = null; @endphp
                @foreach ($cn->lines as $line)
                    @if ($line->brand !== $curBrand)
                        @php $curBrand = $line->brand; @endphp
                        <tr class="brandrow"><td colspan="6">{{ $line->brand }}</td></tr>
                    @endif
                    @php $sn++; @endphp
                    <tr>
                        <td>{{ $sn }}</td>
                        <td>{{ $line->name }}</td>
                        <td class="num">{{ number_format($line->mrp, 2) }}</td>
                        <td class="num">{{ $line->qty }}</td>
                        <td class="num">{{ number_format($line->rate, 2) }}</td>
                        <td class="num">{{ number_format($line->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="totwrap">
        <table>
            <tr>
                <td></td>
                <td style="width:260px;">
                    <table>
                        <tr class="grand"><td>AMOUNT CREDITED</td><td style="text-align:right;">Rs {{ number_format($cn->total, 2) }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="creditmsg">
        Amount credited to the account of {{ $cn->partner->firm_name }}. Outstanding balance payable to {{ $s['firm_name'] ?? 'us' }} stands reduced by this amount.
    </div>

    <div class="foot">
        @if ($cn->kind === 'goods')
            Returned units added back to stock: {{ $cn->lines->sum('qty') }}
        @else
            Amount-only credit note (no goods returned).
        @endif
    </div>

</div>
</body>
</html>
