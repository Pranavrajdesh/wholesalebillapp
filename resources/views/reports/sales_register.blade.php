@extends('layouts.app')

@section('title', 'Sales Register')

@section('content')
    <style>main.container { max-width: 800px; }</style>
    @include('reports._topbar')
    <h2>Sales Register</h2>
    <hr class="rule">

    @php
        $inr = fn ($n) => 'Rs ' . inr($n, 2);
        $qs = fn (array $extra) => http_build_query(array_filter(array_merge(
            ['from' => $from, 'to' => $to, 'partner_id' => $partnerId], $extra
        )));
    @endphp

    <form method="GET" action="{{ route('reports.sales_register') }}">
        <div style="border:1.5px solid #1a1a1a; border-radius:4px; margin:10px 0 14px;">
            <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:flex-end; padding:10px 12px; border-bottom:1px solid #999;">
                <div style="display:flex; flex-direction:column; font-size:11px; color:#555; gap:3px;">
                    <span>From</span><input type="date" name="from" value="{{ $from }}" style="padding:8px; border:1px solid #aaa; border-radius:4px;">
                </div>
                <div style="display:flex; flex-direction:column; font-size:11px; color:#555; gap:3px;">
                    <span>To</span><input type="date" name="to" value="{{ $to }}" style="padding:8px; border:1px solid #aaa; border-radius:4px;">
                </div>
                <div style="display:flex; flex-direction:column; font-size:11px; color:#555; gap:3px; flex:1; min-width:150px;">
                    <span>Partner</span>
                    <select name="partner_id" style="padding:8px; border:1px solid #aaa; border-radius:4px;">
                        <option value="">All partners</option>
                        @foreach ($partners as $p)
                            <option value="{{ $p->id }}" @selected($partnerId == $p->id)>{{ $p->firm_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn" type="submit" style="padding:9px 18px;">APPLY</button>
            </div>
            <div class="chip-grid-4" style="padding:10px 12px;">
                <a class="btn btn-outline" style="padding:9px 4px; font-size:11px; text-decoration:none; text-align:center;" href="{{ route('reports.sales_register') }}?fy=thismonth{{ $partnerId ? '&partner_id=' . $partnerId : '' }}">THIS MONTH</a>
                <a class="btn btn-outline" style="padding:9px 4px; font-size:11px; text-decoration:none; text-align:center;" href="{{ route('reports.sales_register') }}?fy=this{{ $partnerId ? '&partner_id=' . $partnerId : '' }}">THIS FY</a>
                <a class="btn btn-outline" style="padding:9px 4px; font-size:11px; text-decoration:none; text-align:center;" href="{{ route('reports.sales_register') }}?fy=last{{ $partnerId ? '&partner_id=' . $partnerId : '' }}">LAST FY</a>
                <a class="btn btn-outline" style="padding:9px 4px; font-size:11px; text-decoration:none; text-align:center;" href="{{ route('reports.sales_register') }}">ALL</a>
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
        <table style="width:100%; border-collapse:collapse; font-size:12.5px; border:1.5px solid #1a1a1a;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:6px; border:1px solid #555; border-top:1.5px solid #1a1a1a; border-bottom:1.5px solid #1a1a1a; background:#f2f2f2; font-size:11.5px;">Date</th>
                    <th style="text-align:left; padding:6px; border:1px solid #555; border-top:1.5px solid #1a1a1a; border-bottom:1.5px solid #1a1a1a; background:#f2f2f2; font-size:11.5px;">Invoice</th>
                    <th style="text-align:left; padding:6px; border:1px solid #555; border-top:1.5px solid #1a1a1a; border-bottom:1.5px solid #1a1a1a; background:#f2f2f2; font-size:11.5px;">Partner</th>
                    <th style="text-align:right; padding:6px; border:1px solid #555; border-top:1.5px solid #1a1a1a; border-bottom:1.5px solid #1a1a1a; background:#f2f2f2; font-size:11.5px;">Subtotal</th>
                    <th style="text-align:right; padding:6px; border:1px solid #555; border-top:1.5px solid #1a1a1a; border-bottom:1.5px solid #1a1a1a; background:#f2f2f2; font-size:11.5px;">Discount</th>
                    <th style="text-align:right; padding:6px; border:1px solid #555; border-top:1.5px solid #1a1a1a; border-bottom:1.5px solid #1a1a1a; background:#f2f2f2; font-size:11.5px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td style="padding:5px 6px; border:1px solid #999; white-space:nowrap;">{{ \Carbon\Carbon::parse($r->invoice_date)->format('d/m/y') }}</td>
                        <td style="padding:5px 6px; border:1px solid #999;"><a href="{{ route('invoices.show', $r->id) }}" style="color:#1a1a1a; font-weight:700;">INV-{{ $r->invoice_no }}</a></td>
                        <td style="padding:5px 6px; border:1px solid #999;">{{ $r->firm_name }}</td>
                        <td style="padding:5px 6px; border:1px solid #999; text-align:right; white-space:nowrap;">{{ inr($r->subtotal, 2) }}</td>
                        <td style="padding:5px 6px; border:1px solid #999; text-align:right; white-space:nowrap;">{{ $r->discount_amount > 0 ? inr($r->discount_amount, 2) : '' }}</td>
                        <td style="padding:5px 6px; border:1px solid #999; text-align:right; white-space:nowrap; font-weight:700;">{{ inr($r->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:10px 6px; border:1px solid #999; font-weight:600;">No invoices in this period.</td></tr>
                @endforelse
                @if ($rows->count())
                    <tr>
                        <td colspan="3" style="padding:6px; border:1px solid #999; border-top:1.5px solid #1a1a1a; font-weight:700; background:#f7f7f7;">Total</td>
                        <td style="padding:6px; border:1px solid #999; border-top:1.5px solid #1a1a1a; text-align:right; font-weight:700; background:#f7f7f7; white-space:nowrap;">{{ inr($totals['subtotal'], 2) }}</td>
                        <td style="padding:6px; border:1px solid #999; border-top:1.5px solid #1a1a1a; text-align:right; font-weight:700; background:#f7f7f7; white-space:nowrap;">{{ inr($totals['discount'], 2) }}</td>
                        <td style="padding:6px; border:1px solid #999; border-top:1.5px solid #1a1a1a; text-align:right; font-weight:700; background:#f7f7f7; white-space:nowrap;">{{ inr($totals['total'], 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="rcards">
        @foreach ($rows as $r)
            <div class="dcard">
                <div style="display:flex; justify-content:space-between; gap:8px; font-weight:600;">
                    <a href="{{ route('invoices.show', $r->id) }}" style="color:#1a1a1a; font-weight:700;">INV-{{ $r->invoice_no }}</a>
                    <b style="white-space:nowrap;">{{ inr($r->total, 2) }}</b>
                </div>
                <div style="display:flex; justify-content:space-between; gap:8px; margin-top:6px; padding-top:6px; border-top:1px dashed #999; font-size:12.5px;">
                    <span style="color:#444;">{{ \Carbon\Carbon::parse($r->invoice_date)->format('d/m/y') }} &middot; <b style="color:#1a1a1a;">{{ $r->firm_name }}</b></span>
                    @if ($r->discount_amount > 0)<span style="color:#1a1a1a; font-weight:600; white-space:nowrap;">disc {{ inr($r->discount_amount, 2) }}</span>@endif
                </div>
            </div>
        @endforeach
        @if ($rows->count())
            <div class="dcard" style="border-left:4px solid #1a1a1a; font-weight:700; display:flex; justify-content:space-between;">
                <span>Total ({{ $totals['count'] }})</span><span style="white-space:nowrap;">{{ inr($totals['total'], 2) }}</span>
            </div>
        @endif
    </div>

    <style>
        .rcards { display: none; }
        @media (max-width: 640px) {
            .rtblwrap { display: none; }
            .rcards { display: block; }
        }
    </style>

    <div style="margin-top:14px;">
        <a class="btn btn-outline" href="{{ route('reports.index') }}" style="text-decoration:none;">&larr; ALL REPORTS</a>
    </div>
@endsection
