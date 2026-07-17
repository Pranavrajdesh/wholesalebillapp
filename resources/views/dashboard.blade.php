@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <style>
        .statgrid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .statgrid .card { margin: 0; }
        .statval { font-size: 20px; font-weight: 700; white-space: nowrap; }
        .statsub { font-size: 12.5px; font-weight: 600; color: #1a1a1a; margin-top: 2px; }
        .mpos { display: flex; justify-content: space-between; gap: 8px; padding: 7px 0; border-bottom: 1px solid #999; }
        .mpos:last-child { border-bottom: none; }
        .mpos .lbl { font-weight: 600; }
        .mpos a { text-decoration: none; }
        a.alert-line { display: block; text-decoration: none; margin-top: 6px; }
        .chartbars { display: flex; align-items: flex-end; gap: 4px; height: 120px; }
        .chartbars .col { flex: 1; display: flex; flex-direction: column; justify-content: flex-end; height: 100%; }
        .chartbars .bar { background: #1a1a1a; border-radius: 2px 2px 0 0; }
        .chartlbls { display: flex; gap: 4px; margin-top: 4px; }
        .chartlbls div { flex: 1; text-align: center; font-size: 9.5px; font-weight: 600; color: #1a1a1a; }
    </style>

    <h2>Dashboard</h2>
    <hr class="rule">

    @php
        $rs = fn ($n) => 'Rs ' . inr($n, 2);
        $delta = $salesLastMonth > 0 ? (($salesMonth - $salesLastMonth) / $salesLastMonth) * 100 : null;
        $maxSeries = max(1, max(array_column($series, 'value')));
    @endphp

    <div class="statgrid">
        <div class="card">
            <div class="stitle">SALES TODAY</div>
            <div class="statval">{{ $rs($salesToday) }}</div>
            <div class="statsub">{{ $billsToday }} {{ $billsToday === 1 ? 'bill' : 'bills' }}</div>
        </div>
        <div class="card">
            <div class="stitle">RECEIVED TODAY</div>
            <div class="statval">{{ $rs($paymentsToday) }}</div>
            <div class="statsub">payments in</div>
        </div>
        <div class="card">
            <div class="stitle">LAST 7 DAYS</div>
            <div class="statval">{{ $rs($salesWeek) }}</div>
            <div class="statsub">sales</div>
        </div>
        <div class="card">
            <div class="stitle">THIS MONTH</div>
            <div class="statval">{{ $rs($salesMonth) }}</div>
            <div class="statsub {{ $delta === null ? '' : ($delta >= 0 ? 'bal-ok' : 'bal-due') }}">
                @if ($delta === null)
                    collected {{ $rs($paymentsMonth) }}
                @else
                    {!! $delta >= 0 ? '&#9650;' : '&#9660;' !!} {{ inr(abs($delta), 1) }}% vs last month
                @endif
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:10px;">
        <div class="stitle" style="margin-bottom:6px;">MONEY POSITION</div>
        <div class="mpos">
            <span class="lbl">To receive from {{ $duePartners }} {{ $duePartners === 1 ? 'partner' : 'partners' }}</span>
            <a href="{{ route('partners.index') }}" class="bal-due moneyline">{{ $rs($receivable) }}</a>
        </div>
        @if ($advanceHeld > 0)
            <div class="mpos">
                <span class="lbl">Advances held</span>
                <span class="bal-adv moneyline">{{ $rs($advanceHeld) }}</span>
            </div>
        @endif
        <div class="mpos">
            <span class="lbl">To pay {{ $dueSuppliers }} {{ $dueSuppliers === 1 ? 'supplier' : 'suppliers' }}</span>
            <a href="{{ route('suppliers.index') }}" class="bal-due moneyline">{{ $rs($payable) }}</a>
        </div>
    </div>

    @if ($pendingOrders > 0 || $lowStock > 0 || $negativeStock > 0)
        <div class="card" style="margin-top:10px;">
            <div class="stitle" style="margin-bottom:6px;">NEEDS ATTENTION</div>
            @if ($pendingOrders > 0)
                <a href="{{ route('orders.index') }}" class="alert-line callout callout-amber">
                    {{ $pendingOrders }} pending retailer {{ $pendingOrders === 1 ? 'order' : 'orders' }} &rarr;
                </a>
            @endif
            @if ($negativeStock > 0)
                <a href="{{ route('products.index') }}" class="alert-line callout callout-red">
                    {{ $negativeStock }} {{ $negativeStock === 1 ? 'product' : 'products' }} in negative stock &rarr;
                </a>
            @endif
            @if ($lowStock > 0)
                <a href="{{ route('products.index') }}" class="alert-line callout callout-amber">
                    {{ $lowStock }} {{ $lowStock === 1 ? 'product' : 'products' }} low on stock (&le;10) &rarr;
                </a>
            @endif
        </div>
    @endif

    <div class="card" style="margin-top:10px;">
        <div class="stitle" style="margin-bottom:10px;">SALES &mdash; LAST 12 MONTHS</div>
        <div class="chartbars">
            @foreach ($series as $m)
                <div class="col" title="{{ $m['ym'] }}: {{ $rs($m['value']) }}">
                    <div class="bar" style="height:{{ $m['value'] > 0 ? max(3, round($m['value'] / $maxSeries * 100)) : 0 }}%;"></div>
                </div>
            @endforeach
        </div>
        <div class="chartlbls">
            @foreach ($series as $m)
                <div>{{ $m['label'] }}</div>
            @endforeach
        </div>
    </div>
@endsection
