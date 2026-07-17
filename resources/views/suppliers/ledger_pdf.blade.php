<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Ledger &mdash; {{ $supplier->firm_name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        .wrap { border: 2px solid #111; padding: 14px 16px; }
        table { width: 100%; border-collapse: collapse; }
        .headtbl td { border-bottom: 2px solid #111; padding-bottom: 8px; vertical-align: top; }
        .firm { font-size: 16px; font-weight: bold; }
        .firmmeta { font-size: 9px; color: #444; line-height: 1.5; margin-top: 2px; }
        .doctitle { font-size: 9px; letter-spacing: 2px; color: #555; text-align: right; }
        .docperiod { font-size: 10px; text-align: right; }
        .party { margin: 10px 0; font-size: 9.5px; line-height: 1.5; }
        .plabel { font-size: 8px; font-weight: bold; color: #666; letter-spacing: 1px; }
        .pname { font-size: 12px; font-weight: bold; }
        table.led th { background: #f2f2f2; border-top: 1px solid #111; border-bottom: 1px solid #111; padding: 4px 5px; font-size: 8.5px; text-align: left; }
        table.led td { border: 1px solid #ccc; padding: 3px 5px; font-size: 9px; }
        table.led .num { text-align: right; white-space: nowrap; }
        table.led tr.open td, table.led tr.tot td { font-weight: bold; background: #f7f7f7; }
        .detail { font-size: 7.5px; color: #666; }
        .balline { margin-top: 12px; border: 1px solid #bbb; border-left: 4px solid #111; padding: 8px 10px; font-size: 11px; }
        .balline b { font-size: 13px; }
    </style>
</head>
<body>
<div class="wrap">

    <table class="headtbl">
        <tr>
            <td>
                <div class="firm">{{ $s['firm_name'] ?? 'Ledger' }}</div>
                <div class="firmmeta">
                    @if (!empty($s['firm_gst'])) GSTIN: {{ $s['firm_gst'] }}<br>@endif
                    Mobile: {{ $s['firm_mobile'] ?? '' }}
                </div>
            </td>
            <td style="width:190px;">
                <div class="doctitle">SUPPLIER ACCOUNT</div>
                <div class="docperiod">
                    @if ($from || $to)
                        {{ $from ? \Carbon\Carbon::parse($from)->format('d M Y') : 'Start' }}
                        &ndash;
                        {{ $to ? \Carbon\Carbon::parse($to)->format('d M Y') : 'Today' }}
                    @else
                        All transactions
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="party">
        <div class="plabel">ACCOUNT OF</div>
        <div class="pname">{{ $supplier->firm_name }}</div>
        Mobile: {{ $supplier->mobile }}
        @if ($supplier->gst_number)<br>GSTIN: {{ $supplier->gst_number }}@endif
    </div>

    <table class="led">
        <thead>
            <tr>
                <th style="width:62px;">Date</th>
                <th>Particulars</th>
                <th class="num" style="width:70px;">Debit</th>
                <th class="num" style="width:70px;">Credit</th>
                <th class="num" style="width:80px;">Balance</th>
            </tr>
        </thead>
        <tbody>
            @if ($from)
                <tr class="open">
                    <td></td><td>Opening balance</td>
                    <td class="num"></td><td class="num"></td>
                    <td class="num">Rs {{ number_format($ledger['opening'], 2) }}</td>
                </tr>
            @endif
            @foreach ($ledger['entries'] as $e)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($e['date'])->format('d/m/y') }}</td>
                    <td>{{ $e['label'] }}@if ($e['detail'])<div class="detail">{{ $e['detail'] }}</div>@endif</td>
                    <td class="num">{{ $e['debit'] > 0 ? 'Rs ' . number_format($e['debit'], 2) : '' }}</td>
                    <td class="num">{{ $e['credit'] > 0 ? 'Rs ' . number_format($e['credit'], 2) : '' }}</td>
                    <td class="num">Rs {{ number_format($e['balance'], 2) }}</td>
                </tr>
            @endforeach
            @if (count($ledger['entries']))
                <tr class="tot">
                    <td></td><td>Total</td>
                    <td class="num">Rs {{ number_format($ledger['total_debit'], 2) }}</td>
                    <td class="num">Rs {{ number_format($ledger['total_credit'], 2) }}</td>
                    <td class="num">Rs {{ number_format($ledger['closing'], 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="balline">
        @if ($ledger['closing'] > 0)
            Payable to {{ $supplier->firm_name }}: <b>Rs {{ number_format($ledger['closing'], 2) }}</b>
        @elseif ($ledger['closing'] < 0)
            Advance paid to {{ $supplier->firm_name }}: <b>Rs {{ number_format(abs($ledger['closing']), 2) }}</b>
        @else
            Account settled: <b>Rs 0.00</b>
        @endif
    </div>

</div>
</body>
</html>
