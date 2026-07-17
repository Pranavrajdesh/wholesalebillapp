@extends('layouts.app')

@section('title', 'Sales Summary')

@section('content')
    <style>main.container { max-width: 800px; }</style>
    @include('reports._topbar')
    <h2>Sales Summary</h2>
    <hr class="rule">

    @php
        $base = fn (array $extra) => route('reports.sales_summary') . '?' . http_build_query(array_filter(array_merge(
            ['group' => $group, 'from' => $from, 'to' => $to], $extra
        )));
        $isLine = in_array($group, ['brand', 'product'], true);
    @endphp

    <form method="GET" action="{{ route('reports.sales_summary') }}">
        <input type="hidden" name="group" value="{{ $group }}">
        <div class="fbox">
            <div class="frow">
                <div class="f"><span>From</span><input type="date" name="from" value="{{ $from }}"></div>
                <div class="f"><span>To</span><input type="date" name="to" value="{{ $to }}"></div>
                <button class="btn" type="submit" style="padding:9px 18px;">APPLY</button>
            </div>
            <div class="chip-grid-4">
                <a class="btn {{ ($fyActive ?? '') === 'thismonth' ? '' : 'btn-outline' }}" href="{{ $base(['from' => null, 'to' => null]) }}&fy=thismonth">THIS MONTH</a>
                <a class="btn {{ ($fyActive ?? '') === 'this' ? '' : 'btn-outline' }}" href="{{ $base(['from' => null, 'to' => null]) }}&fy=this">THIS FY</a>
                <a class="btn {{ ($fyActive ?? '') === 'last' ? '' : 'btn-outline' }}" href="{{ $base(['from' => null, 'to' => null]) }}&fy=last">LAST FY</a>
                <a class="btn {{ ($fyActive ?? '') === 'all' ? '' : 'btn-outline' }}" href="{{ $base(['from' => null, 'to' => null]) }}">ALL</a>
            </div>
            <div class="chip-grid-4">
                @foreach (['month' => 'BY MONTH', 'partner' => 'BY PARTNER', 'brand' => 'BY BRAND', 'product' => 'BY PRODUCT'] as $g => $label)
                    <a href="{{ route('reports.sales_summary') }}?{{ http_build_query(array_filter(['group' => $g, 'from' => $from, 'to' => $to])) }}"
                       class="btn {{ $group === $g ? '' : 'btn-outline' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </form>

    @include('reports._actions', [
        'count_label' => $rows->count() . ' ' . ($rows->count() === 1 ? 'row' : 'rows'),
        'csv_url' => $base(['format' => 'csv']),
        'pdf_url' => $base(['format' => 'pdf']),
        'clear_url' => route('reports.sales_summary') . '?group=' . $group,
        'filters_active' => (bool) ($from || $to),
    ])

    @if ($isLine)
        <p class="callout" style="margin:0 0 8px;">
            Amounts are gross line values (before invoice-level discount and round-off).
        </p>
    @endif

    <div class="rtblwrap" style="overflow-x:auto;">
        <table class="rtable">
            <thead>
                <tr>
                    <th>{{ ucfirst($group) }}</th>
                    @if ($isLine)
                        <th class="num" style="width:80px;">Qty</th>
                        <th class="num" style="width:80px;">Free</th>
                    @else
                        <th class="num" style="width:80px;">Bills</th>
                    @endif
                    <th class="num" style="width:130px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td style="font-weight:600;">{{ $r->g }}</td>
                        @if ($isLine)
                            <td class="num">{{ $r->qty }}</td>
                            <td class="num">{{ $r->free > 0 ? $r->free : '' }}</td>
                        @else
                            <td class="num">{{ $r->bills }}</td>
                        @endif
                        <td class="num" style="font-weight:700;">{{ inr($r->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="{{ $isLine ? 4 : 3 }}" style="font-weight:600; padding:10px 6px;">No sales in this period.</td></tr>
                @endforelse
                @if ($rows->count())
                    <tr class="tot">
                        <td colspan="{{ $isLine ? 3 : 2 }}">Total</td>
                        <td class="num">{{ inr($totalAmount, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="rcards">
        @foreach ($rows as $r)
            <div class="dcard">
                <div class="dcard-row" style="font-weight:600;">
                    <span style="font-weight:700;">{{ $r->g }}</span>
                    <b class="moneyline">{{ inr($r->amount, 2) }}</b>
                </div>
                <div class="dcard-part" style="font-size:12.5px; font-weight:600;">
                    @if ($r->bills !== null) {{ $r->bills }} {{ $r->bills == 1 ? 'bill' : 'bills' }} @else Qty {{ $r->qty }}@if ($r->free > 0) + {{ $r->free }} free @endif @endif
                </div>
            </div>
        @endforeach
        @if ($rows->count())
            <div class="dcard dcard-row" style="border-left:4px solid #1a1a1a; font-weight:700;">
                <span>Total</span><span class="moneyline">{{ inr($totalAmount, 2) }}</span>
            </div>
        @endif
    </div>

    <div style="margin-top:14px;">
        <a class="btn btn-outline" href="{{ route('reports.index') }}" style="text-decoration:none;">&larr; ALL REPORTS</a>
    </div>
@endsection
