@extends('layouts.app')

@section('title', 'Purchase Register')

@section('content')
    <style>
        main.container { max-width: 800px; }

        .sect { margin-top: 16px; }

    </style>

    @include('reports._topbar')

    <h2>Purchase Register</h2>
    <hr class="rule">

    @php $qs = http_build_query(array_filter(['from' => $from, 'to' => $to])); @endphp

    <form method="GET" action="{{ route('reports.purchases') }}">
        <div class="fbox">
            <div class="frow">
                <div class="f"><span>From</span><input type="date" name="from" value="{{ $from }}"></div>
                <div class="f"><span>To</span><input type="date" name="to" value="{{ $to }}"></div>
                <button class="btn" type="submit" style="padding:9px 18px;">APPLY</button>
            </div>
            <div class="chip-grid-4" style="padding:10px 12px;">
                <a class="btn {{ ($fyActive ?? '') === 'thismonth' ? '' : 'btn-outline' }}" href="{{ route('reports.purchases') }}?fy=thismonth">THIS MONTH</a>
                <a class="btn {{ ($fyActive ?? '') === 'this' ? '' : 'btn-outline' }}" href="{{ route('reports.purchases') }}?fy=this">THIS FY</a>
                <a class="btn {{ ($fyActive ?? '') === 'last' ? '' : 'btn-outline' }}" href="{{ route('reports.purchases') }}?fy=last">LAST FY</a>
                <a class="btn {{ ($fyActive ?? '') === 'all' ? '' : 'btn-outline' }}" href="{{ route('reports.purchases') }}">ALL</a>
            </div>
        </div>
    </form>

    @include('reports._actions', [
        'count_label' => 'Bills: ' . $bills->count() . ' · Inward: ' . $inward->count(),
        'csv_url' => route('reports.purchases') . '?' . ($qs ? $qs . '&' : '') . 'format=csv',
        'pdf_url' => route('reports.purchases') . '?' . ($qs ? $qs . '&' : '') . 'format=pdf',
        'clear_url' => route('reports.purchases'),
        'filters_active' => (bool) ($from || $to),
    ])

    <div class="stitle" style="margin-bottom:4px;">SUPPLIER BILLS ({{ $bills->count() }})</div>

    <div class="rtblwrap" style="overflow-x:auto;">
        <table class="rtable">
            <thead>
                <tr>
                    <th style="width:78px;">Date</th>
                    <th>Supplier</th>
                    <th>Bill No</th>
                    <th class="num" style="width:110px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bills as $b)
                    <tr>
                        <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($b->bill_date)->format('d/m/y') }}</td>
                        <td style="font-weight:600;">{{ $b->firm_name }}</td>
                        <td>{{ $b->bill_no }}{{ $b->note ? ($b->bill_no ? ' · ' : '') . $b->note : '' }}</td>
                        <td class="num" style="font-weight:700;">{{ inr($b->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="font-weight:600; padding:10px 6px;">No supplier bills in this period.</td></tr>
                @endforelse
                @if ($bills->count())
                    <tr class="tot"><td colspan="3">Total</td><td class="num">{{ inr($billTotal, 2) }}</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="rcards">
        @foreach ($bills as $b)
            <div class="dcard">
                <div class="dcard-row" style="font-weight:600;">
                    <span>{{ $b->firm_name }}</span>
                    <b class="moneyline">{{ inr($b->amount, 2) }}</b>
                </div>
                <div class="dcard-part" style="font-size:12.5px; color:#444;">{{ \Carbon\Carbon::parse($b->bill_date)->format('d/m/y') }}{{ $b->bill_no ? ' · ' . $b->bill_no : '' }}</div>
            </div>
        @endforeach
        @if ($bills->count())
            <div class="dcard" style="border-left:4px solid #1a1a1a; font-weight:700;">
                <div class="dcard-row"><span>Bills total</span><span class="moneyline">{{ inr($billTotal, 2) }}</span></div>
            </div>
        @endif
    </div>

    <div class="sect">
        <div class="stitle" style="margin-bottom:4px;">STOCK INWARD ({{ $inward->count() }})</div>

        <div class="rtblwrap" style="overflow-x:auto;">
            <table class="rtable">
                <thead>
                    <tr>
                        <th style="width:78px;">Date</th>
                        <th>Supplier</th>
                        <th class="num" style="width:70px;">Items</th>
                        <th class="num" style="width:80px;">Units</th>
                        <th class="num" style="width:120px;">Value (known rates)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inward as $i)
                        <tr>
                            <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($i->inward_date)->format('d/m/y') }}</td>
                            <td style="font-weight:600;"><a href="{{ url('/inward/' . $i->id) }}" style="color:#1a1a1a;">{{ $i->firm_name }}</a></td>
                            <td class="num">{{ $i->line_count }}</td>
                            <td class="num">{{ $i->units }}</td>
                            <td class="num" style="font-weight:700;">{{ $i->value > 0 ? inr($i->value, 2) : '' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="font-weight:600; padding:10px 6px;">No inward entries in this period.</td></tr>
                    @endforelse
                    @if ($inward->count())
                        <tr class="tot"><td colspan="3">Total</td><td class="num">{{ inr($inwardUnits) }}</td><td class="num">{{ inr($inwardValue, 2) }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="rcards">
            @foreach ($inward as $i)
                <div class="dcard">
                    <div class="dcard-row" style="font-weight:600;">
                        <a href="{{ url('/inward/' . $i->id) }}" style="color:#1a1a1a; font-weight:700;">{{ $i->firm_name }}</a>
                        <b class="moneyline">+{{ $i->units }}</b>
                    </div>
                    <div class="dcard-part" style="font-size:12.5px; color:#444;">{{ \Carbon\Carbon::parse($i->inward_date)->format('d/m/y') }} &middot; {{ $i->line_count }} items{{ $i->value > 0 ? ' · Rs ' . inr($i->value, 2) : '' }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <p class="callout" style="margin-top:12px;">
        Bills and inward are recorded separately &mdash; bills drive the supplier ledger; inward drives stock.
        Inward value counts only lines where a purchase rate was entered.
    </p>

    <div style="margin-top:14px;">
        <a class="btn btn-outline" href="{{ route('reports.index') }}" style="text-decoration:none;">&larr; ALL REPORTS</a>
    </div>
@endsection
