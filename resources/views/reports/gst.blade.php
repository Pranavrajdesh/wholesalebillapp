@extends('layouts.app')

@section('title', 'GST / HSN Summary')

@section('content')
    <style>
        main.container { max-width: 800px; }


    </style>

    @include('reports._topbar')

    <h2>GST / HSN Summary</h2>
    <hr class="rule">

    @php $qs = http_build_query(array_filter(['from' => $from, 'to' => $to])); @endphp

    <form method="GET" action="{{ route('reports.gst') }}">
        <div class="fbox">
            <div class="frow">
                <div class="f"><span>From</span><input type="date" name="from" value="{{ $from }}"></div>
                <div class="f"><span>To</span><input type="date" name="to" value="{{ $to }}"></div>
                <button class="btn" type="submit" style="padding:9px 18px;">APPLY</button>
            </div>
            <div class="chip-grid-4" style="padding:10px 12px;">
                <a class="btn {{ ($fyActive ?? '') === 'thismonth' ? '' : 'btn-outline' }}" href="{{ route('reports.gst') }}?fy=thismonth">THIS MONTH</a>
                <a class="btn {{ ($fyActive ?? '') === 'this' ? '' : 'btn-outline' }}" href="{{ route('reports.gst') }}?fy=this">THIS FY</a>
                <a class="btn {{ ($fyActive ?? '') === 'last' ? '' : 'btn-outline' }}" href="{{ route('reports.gst') }}?fy=last">LAST FY</a>
                <a class="btn {{ ($fyActive ?? '') === 'all' ? '' : 'btn-outline' }}" href="{{ route('reports.gst') }}">ALL</a>
            </div>
            <div class="chip-grid-4">
                <a class="btn {{ ($fyActive ?? '') === 'q1' ? '' : 'btn-outline' }}" href="{{ route('reports.gst') }}?fy=q1">Q1 APR&ndash;JUN</a>
                <a class="btn {{ ($fyActive ?? '') === 'q2' ? '' : 'btn-outline' }}" href="{{ route('reports.gst') }}?fy=q2">Q2 JUL&ndash;SEP</a>
                <a class="btn {{ ($fyActive ?? '') === 'q3' ? '' : 'btn-outline' }}" href="{{ route('reports.gst') }}?fy=q3">Q3 OCT&ndash;DEC</a>
                <a class="btn {{ ($fyActive ?? '') === 'q4' ? '' : 'btn-outline' }}" href="{{ route('reports.gst') }}?fy=q4">Q4 JAN&ndash;MAR</a>
            </div>
        </div>
    </form>

    @include('reports._actions', [
        'count_label' => $rows->count() . ' HSN ' . ($rows->count() === 1 ? 'group' : 'groups'),
        'csv_url' => route('reports.gst') . '?' . ($qs ? $qs . '&' : '') . 'format=csv',
        'pdf_url' => route('reports.gst') . '?' . ($qs ? $qs . '&' : '') . 'format=pdf',
        'clear_url' => route('reports.gst'),
        'filters_active' => (bool) ($from || $to),
    ])

    <p class="callout" style="margin-bottom:10px;">
        Taxable values are backed out of tax-inclusive line amounts (taxable = amount &divide; (1 + rate)).
        Tax split as CGST + SGST. Matches the tax summary printed on each invoice.
    </p>

    <div class="rtblwrap" style="overflow-x:auto;">
        <table class="rtable">
            <thead>
                <tr>
                    <th>HSN</th>
                    <th class="num" style="width:60px;">GST %</th>
                    <th class="num">Taxable</th>
                    <th class="num">CGST</th>
                    <th class="num">SGST</th>
                    <th class="num">Total Tax</th>
                    <th class="num">Gross</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td style="font-weight:600;">{{ $r->hsn }}</td>
                        <td class="num">{{ rtrim(rtrim(inr($r->pct, 2), '0'), '.') }}%</td>
                        <td class="num">{{ inr($r->taxable, 2) }}</td>
                        <td class="num">{{ inr($r->cgst, 2) }}</td>
                        <td class="num">{{ inr($r->sgst, 2) }}</td>
                        <td class="num" style="font-weight:700;">{{ inr($r->tax, 2) }}</td>
                        <td class="num">{{ inr($r->gross, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="font-weight:600; padding:10px 6px;">No taxed sales in this period.</td></tr>
                @endforelse
                @if ($rows->count())
                    <tr class="tot">
                        <td colspan="2">Total</td>
                        <td class="num">{{ inr($sumTaxable, 2) }}</td>
                        <td class="num">{{ inr($sumTax / 2, 2) }}</td>
                        <td class="num">{{ inr($sumTax / 2, 2) }}</td>
                        <td class="num">{{ inr($sumTax, 2) }}</td>
                        <td class="num">{{ inr($sumGross, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="rcards">
        @foreach ($rows as $r)
            <div class="dcard">
                <div class="dcard-row" style="font-weight:600;">
                    <span>HSN {{ $r->hsn }} &middot; {{ rtrim(rtrim(inr($r->pct, 2), '0'), '.') }}%</span>
                    <b class="moneyline">{{ inr($r->tax, 2) }}</b>
                </div>
                <div class="dcard-part" style="font-size:12.5px; font-weight:600; color:#1a1a1a;">
                    Taxable {{ inr($r->taxable, 2) }} &middot; CGST {{ inr($r->cgst, 2) }} &middot; SGST {{ inr($r->sgst, 2) }}
                </div>
            </div>
        @endforeach
        @if ($rows->count())
            <div class="dcard" style="border-left:4px solid #1a1a1a; font-weight:700;">
                <div class="dcard-row"><span>Total tax</span><span class="moneyline">{{ inr($sumTax, 2) }}</span></div>
            </div>
        @endif
    </div>

    @if ($exemptTotal > 0)
        <p class="callout" style="margin-top:10px;">
            Zero-GST / untaxed line value in this period: <b>Rs {{ inr($exemptTotal, 2) }}</b>
        </p>
    @endif

    <div style="margin-top:14px;">
        <a class="btn btn-outline" href="{{ route('reports.index') }}" style="text-decoration:none;">&larr; ALL REPORTS</a>
    </div>
@endsection
