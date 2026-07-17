<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ledger &mdash; {{ $partner->firm_name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        .wrap { border: 2px solid #111; padding: 14px 16px; }
        table { width: 100%; border-collapse: collapse; }
        .headtbl td { border-bottom: 2px solid #111; padding-bottom: 8px; vertical-align: top; }
        .firm { font-size: 16px; font-weight: bold; }
        .firmmeta { font-size: 9.5px; font-weight: bold; color: #1a1a1a; line-height: 1.6; margin-top: 2px; }
        .doctitle { font-size: 10px; font-weight: bold; letter-spacing: 2px; color: #1a1a1a; text-align: right; }
        .docperiod { font-size: 10px; text-align: right; margin-top: 2px; }
        .party { margin: 10px 0; font-size: 9.5px; line-height: 1.5; }
        .plabel { font-size: 9px; font-weight: bold; color: #1a1a1a; letter-spacing: 1px; }
        .pname { font-size: 12px; font-weight: bold; }
        table.led th { background: #f2f2f2; border: 1px solid #888; border-top: 1px solid #111; border-bottom: 1px solid #111; padding: 4px 5px; font-size: 8.5px; text-align: left; }
        table.led td { border: 1px solid #bbb; padding: 3px 5px; font-size: 9.5px; vertical-align: top; }
        table.led .num { text-align: right; white-space: nowrap; }
        table.led tr.open td, table.led tr.tot td { font-weight: bold; background: #f0f0f0; }
        .detail { font-size: 8px; color: #666; }
        .balbox { margin-top: 12px; border: 1px solid #999; border-left: 4px solid #111; padding: 8px 10px; font-size: 11px; }
        .balbox b { font-size: 13px; }
        .paytbl { margin-top: 12px; border: 1px solid #999; }
        .paytbl td { padding: 8px 10px; font-size: 9.5px; line-height: 1.6; }
        .paytbl .qrcell { width: 130px; text-align: center; font-size: 8.5px; color: #444; }
        .foot { margin-top: 12px; font-size: 9.5px; font-weight: bold; color: #1a1a1a; line-height: 1.6; }
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
                    Mobile: {{ $s['firm_mobile'] ?? '' }}@if (!empty($s['firm_alt_mobile'])), {{ $s['firm_alt_mobile'] }}@endif
                    @if (!empty($s['firm_address']))<br>{{ $s['firm_address'] }}@endif
                </div>
            </td>
            <td style="width:200px;">
                <div class="doctitle">ACCOUNT STATEMENT</div>
                <div class="docperiod">
                    @if ($from || $to)
                        {{ $from ? date('d M Y', strtotime($from)) : 'Start' }} &ndash; {{ $to ? date('d M Y', strtotime($to)) : 'Today' }}
                    @else
                        All transactions
                    @endif
                </div>
                <div class="docperiod">Generated: {{ now()->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="party">
        <div class="plabel">ACCOUNT OF</div>
        <div class="pname">{{ $partner->firm_name }}</div>
        Mobile: {{ $partner->mobile }}
        @if ($partner->gst_number)<br>GSTIN: {{ $partner->gst_number }}@endif
    </div>

    <table class="led">
        <thead>
            <tr>
                <th style="width:64px;">Date</th>
                <th>Particulars</th>
                <th class="num" style="width:80px;">Debit</th>
                <th class="num" style="width:80px;">Credit</th>
                <th class="num" style="width:90px;">Balance</th>
            </tr>
        </thead>
        <tbody>
            @if ($from)
                <tr class="open">
                    <td></td>
                    <td>Opening balance</td>
                    <td class="num"></td>
                    <td class="num"></td>
                    <td class="num">{{ number_format($ledger['opening'], 2) }}</td>
                </tr>
            @endif
            @foreach ($ledger['entries'] as $e)
                <tr>
                    <td>{{ date('d/m/y', strtotime($e['date'])) }}</td>
                    <td>
                        {{ $e['label'] }}
                        @if (!empty($e['detail']))<div class="detail">{{ $e['detail'] }}</div>@endif
                    </td>
                    <td class="num">{{ $e['debit'] > 0 ? number_format($e['debit'], 2) : '' }}</td>
                    <td class="num">{{ $e['credit'] > 0 ? number_format($e['credit'], 2) : '' }}</td>
                    <td class="num">{{ number_format($e['balance'], 2) }}</td>
                </tr>
            @endforeach
            @if (count($ledger['entries']))
                <tr class="tot">
                    <td></td>
                    <td>Total</td>
                    <td class="num">{{ number_format($ledger['total_debit'], 2) }}</td>
                    <td class="num">{{ number_format($ledger['total_credit'], 2) }}</td>
                    <td class="num">{{ number_format($ledger['closing'], 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="balbox">
        @if ($ledger['closing'] > 0)
            Balance due from {{ $partner->firm_name }}: <b>Rs {{ number_format($ledger['closing'], 2) }}</b>
        @elseif ($ledger['closing'] < 0)
            Advance held: <b>Rs {{ number_format(abs($ledger['closing']), 2) }}</b>
        @else
            Account settled: <b>Rs 0.00</b>
        @endif
    </div>

    @php $hasBank = !empty($s['bank_account']) && !empty($s['bank_ifsc']); @endphp
    @if ($hasBank || $qrDataUri)
        <table class="paytbl">
            <tr>
                <td>
                    <div style="font-size:9px; font-weight:bold; color:#1a1a1a; letter-spacing:1px;">USE FOLLOWING BANK DETAILS FOR PAYMENTS</div>
                    @if ($hasBank)
                        @if (!empty($s['bank_holder'])){{ $s['bank_holder'] }}<br>@endif
                        @if (!empty($s['bank_name'])){{ $s['bank_name'] }}<br>@endif
                        A/c: <b>{{ $s['bank_account'] }}</b> &nbsp;|&nbsp; IFSC: <b>{{ $s['bank_ifsc'] }}</b><br>
                    @endif
                    @if (!empty($s['upi_id']))UPI: {{ $s['upi_id'] }}@endif
                </td>
                @if ($qrDataUri)
                    <td class="qrcell">
                        <img src="{{ $qrDataUri }}" width="100" height="100"><br>
                        Scan &amp; pay via UPI
                    </td>
                @endif
            </tr>
        </table>
    @endif

    <div class="foot">
        For any clarification please get in touch with us.<br>
        This is a computer-generated statement. Debit = bills issued, Credit = payments received.
    </div>

</div>
</body>
</html>
