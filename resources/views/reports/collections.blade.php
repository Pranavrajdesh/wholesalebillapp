@extends('layouts.app')

@section('title', 'Collections Register')

@section('content')
    <style>
        main.container { max-width: 800px; }
        .fbox { border: 1.5px solid #1a1a1a; border-radius: 4px; margin: 10px 0 14px; }
        .fbox .frow { display: flex; gap: 8px; flex-wrap: wrap; align-items: flex-end; padding: 10px 12px; border-bottom: 1px solid #999; }
        .fbox .frow.last { border-bottom: none; }
        .fbox .f { display: flex; flex-direction: column; font-size: 11px; color: #555; gap: 3px; }
        .fbox input, .fbox select { padding: 8px; border: 1px solid #aaa; border-radius: 4px; font-size: 13px; }
        .methodstrip { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 10px; }
        .methodstrip .m { border: 1px solid #1a1a1a; border-radius: 4px; padding: 6px 12px; font-size: 12.5px; font-weight: 600; }
        .rcards { display: none; }
        @media (max-width: 640px) {
            .rtblwrap { display: none; }
            .rcards { display: block; }
        }
    </style>

    @include('reports._topbar')

    <h2>Collections Register</h2>
    <hr class="rule">

    @php
        $qs = fn (array $extra) => http_build_query(array_filter(array_merge(
            ['from' => $from, 'to' => $to, 'partner_id' => $partnerId], $extra
        )));
    @endphp

    <form method="GET" action="{{ route('reports.collections') }}">
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
            <div class="chip-grid-4" style="padding:10px 12px;">
                <a class="btn {{ ($fyActive ?? '') === 'thismonth' ? '' : 'btn-outline' }}" href="{{ route('reports.collections') }}?fy=thismonth{{ $partnerId ? '&partner_id=' . $partnerId : '' }}">THIS MONTH</a>
                <a class="btn {{ ($fyActive ?? '') === 'this' ? '' : 'btn-outline' }}" href="{{ route('reports.collections') }}?fy=this{{ $partnerId ? '&partner_id=' . $partnerId : '' }}">THIS FY</a>
                <a class="btn {{ ($fyActive ?? '') === 'last' ? '' : 'btn-outline' }}" href="{{ route('reports.collections') }}?fy=last{{ $partnerId ? '&partner_id=' . $partnerId : '' }}">LAST FY</a>
                <a class="btn {{ ($fyActive ?? '') === 'all' ? '' : 'btn-outline' }}" href="{{ route('reports.collections') }}">ALL</a>
            </div>
        </div>
    </form>

    @include('reports._actions', [
        'count_label' => $rows->count() . ' ' . ($rows->count() === 1 ? 'payment' : 'payments'),
        'csv_url' => route('reports.collections') . '?' . $qs(['format' => 'csv']),
        'pdf_url' => route('reports.collections') . '?' . $qs(['format' => 'pdf']),
        'clear_url' => route('reports.collections'),
        'filters_active' => (bool) ($from || $to || $partnerId),
    ])

    @if ($byMethod->count())
        <div class="methodstrip">
            @foreach ($byMethod as $method => $amt)
                <div class="m">{{ strtoupper($method) }}: Rs {{ inr($amt, 2) }}</div>
            @endforeach
        </div>
    @endif

    <div class="rtblwrap" style="overflow-x:auto;">
        <table class="rtable">
            <thead>
                <tr>
                    <th style="width:78px;">Date</th>
                    <th>Partner</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th class="num" style="width:110px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($r->payment_date)->format('d/m/y') }}</td>
                        <td style="font-weight:600;">{{ $r->firm_name }}</td>
                        <td>{{ strtoupper($r->method) }}</td>
                        <td>{{ $r->reference }}{{ $r->note ? ($r->reference ? ' · ' : '') . $r->note : '' }}</td>
                        <td class="num" style="font-weight:700;">{{ inr($r->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="font-weight:600; padding:10px 6px;">No payments in this period.</td></tr>
                @endforelse
                @if ($rows->count())
                    <tr class="tot">
                        <td colspan="4">Total</td>
                        <td class="num">{{ inr($total, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="rcards">
        @foreach ($rows as $r)
            <div class="dcard">
                <div class="dcard-row" style="font-weight:600;">
                    <span>{{ $r->firm_name }}</span>
                    <b class="moneyline">{{ inr($r->amount, 2) }}</b>
                </div>
                <div class="dcard-row dcard-part" style="font-size:12.5px; color:#444;">
                    <span>{{ \Carbon\Carbon::parse($r->payment_date)->format('d/m/y') }} &middot; <b style="color:#1a1a1a;">{{ strtoupper($r->method) }}</b>{{ $r->reference ? ' · ' . $r->reference : '' }}</span>
                </div>
            </div>
        @endforeach
        @if ($rows->count())
            <div class="dcard" style="border-left:4px solid #1a1a1a; font-weight:700;">
                <div class="dcard-row"><span>Total</span><span class="moneyline">{{ inr($total, 2) }}</span></div>
            </div>
        @endif
    </div>

    <div style="margin-top:14px;">
        <a class="btn btn-outline" href="{{ route('reports.index') }}" style="text-decoration:none;">&larr; ALL REPORTS</a>
    </div>
@endsection
