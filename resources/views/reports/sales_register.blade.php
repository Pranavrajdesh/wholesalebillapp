@extends('layouts.app')

@section('title', 'Sales Register')

@section('content')
    <style>main.container { max-width: 800px; }</style>
    @include('reports._topbar')
    <h2>Sales Register</h2>
    <hr class="rule">

    @php
        $qs = fn (array $extra) => http_build_query(array_filter(array_merge(
            ['from' => $from, 'to' => $to, 'partner_id' => $partnerId], $extra
        )));
        $pq = $partnerId ? '&partner_id=' . $partnerId : '';
    @endphp

    <form method="GET" action="{{ route('reports.sales_register') }}">
        <div class="fbox">
            <div class="frow">
                <div class="f"><span>From</span><input type="date" name="from" value="{{ $from }}"></div>
                <div class="f"><span>To</span><input type="date" name="to" value="{{ $to }}"></div>
                <div class="f" style="flex:1; min-width:150px;">
                    <span>Partner</span>
                    <select name="partner_id">
                        <option value="">All partners</option>
                        @foreach ($partners as $p)
                            <option value="{{ $p->id }}" @selected($partnerId == $p->id)>{{ $p->firm_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn" type="submit" style="padding:9px 18px;">APPLY</button>
            </div>
            <div class="chip-grid-4">
                <a class="btn {{ ($fyActive ?? '') === 'thismonth' ? '' : 'btn-outline' }}" href="{{ route('reports.sales_register') }}?fy=thismonth{{ $pq }}">THIS MONTH</a>
                <a class="btn {{ ($fyActive ?? '') === 'this' ? '' : 'btn-outline' }}" href="{{ route('reports.sales_register') }}?fy=this{{ $pq }}">THIS FY</a>
                <a class="btn {{ ($fyActive ?? '') === 'last' ? '' : 'btn-outline' }}" href="{{ route('reports.sales_register') }}?fy=last{{ $pq }}">LAST FY</a>
                <a class="btn {{ ($fyActive ?? '') === 'all' ? '' : 'btn-outline' }}" href="{{ route('reports.sales_register') }}">ALL</a>
            </div>
        </div>
    </form>

    @include('reports._actions', [
        'count_label' => $totals['count'] . ' ' . ($totals['count'] === 1 ? 'invoice' : 'invoices'),
        'csv_url' => route('reports.sales_register') . '?' . $qs(['format' => 'csv']),
        'pdf_url' => route('reports.sales_register') . '?' . $qs(['format' => 'pdf']),
        'clear_url' => route('reports.sales_register'),
        'filters_active' => (bool) ($from || $to || $partnerId),
    ])

    <div class="rtblwrap" style="overflow-x:auto;">
        <table class="rtable">
            <thead>
                <tr>
                    <th style="width:78px;">Date</th>
                    <th>Invoice</th>
                    <th>Partner</th>
                    <th class="num">Subtotal</th>
                    <th class="num">Discount</th>
                    <th class="num" style="width:110px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($r->invoice_date)->format('d/m/y') }}</td>
                        <td><a href="{{ route('invoices.show', $r->id) }}" style="color:#1a1a1a; font-weight:700;">INV-{{ $r->invoice_no }}</a></td>
                        <td>{{ $r->firm_name }}</td>
                        <td class="num">{{ inr($r->subtotal, 2) }}</td>
                        <td class="num">{{ $r->discount_amount > 0 ? inr($r->discount_amount, 2) : '' }}</td>
                        <td class="num" style="font-weight:700;">{{ inr($r->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="font-weight:600; padding:10px 6px;">No invoices in this period.</td></tr>
                @endforelse
                @if ($rows->count())
                    <tr class="tot">
                        <td colspan="3">Total</td>
                        <td class="num">{{ inr($totals['subtotal'], 2) }}</td>
                        <td class="num">{{ inr($totals['discount'], 2) }}</td>
                        <td class="num">{{ inr($totals['total'], 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="rcards">
        @foreach ($rows as $r)
            <div class="dcard">
                <div class="dcard-row" style="font-weight:600;">
                    <a href="{{ route('invoices.show', $r->id) }}" style="color:#1a1a1a; font-weight:700;">INV-{{ $r->invoice_no }}</a>
                    <b class="moneyline">{{ inr($r->total, 2) }}</b>
                </div>
                <div class="dcard-row dcard-part" style="font-size:12.5px;">
                    <span style="color:#444;">{{ \Carbon\Carbon::parse($r->invoice_date)->format('d/m/y') }} &middot; <b style="color:#1a1a1a;">{{ $r->firm_name }}</b></span>
                    @if ($r->discount_amount > 0)<span class="moneyline" style="font-weight:600;">disc {{ inr($r->discount_amount, 2) }}</span>@endif
                </div>
            </div>
        @endforeach
        @if ($rows->count())
            <div class="dcard dcard-row" style="border-left:4px solid #1a1a1a; font-weight:700;">
                <span>Total ({{ $totals['count'] }})</span><span class="moneyline">{{ inr($totals['total'], 2) }}</span>
            </div>
        @endif
    </div>

    <div style="margin-top:14px;">
        <a class="btn btn-outline" href="{{ route('reports.index') }}" style="text-decoration:none;">&larr; ALL REPORTS</a>
    </div>
@endsection
