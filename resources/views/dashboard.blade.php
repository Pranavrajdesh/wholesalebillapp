@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h2>Dashboard</h2>
    <hr class="rule">

    @php
        $inr = fn ($n) => 'Rs ' . inr($n, 2);
        $delta = $salesLastMonth > 0 ? (($salesMonth - $salesLastMonth) / $salesLastMonth) * 100 : null;
        $maxSeries = max(1, max(array_column($series, 'value')));
    @endphp

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div class="card" style="margin:0;">
            <div style="font-size:11.5px; font-weight:700; letter-spacing:0.5px; color:#1a1a1a;">SALES TODAY</div>
            <div style="font-size:20px; font-weight:700; white-space:nowrap;">{{ $inr($salesToday) }}</div>
            <div style="font-size:12.5px; font-weight:600; color:#1a1a1a; margin-top:2px;">{{ $billsToday }} {{ $billsToday === 1 ? 'bill' : 'bills' }}</div>
        </div>
        <div class="card" style="margin:0;">
            <div style="font-size:11.5px; font-weight:700; letter-spacing:0.5px; color:#1a1a1a;">RECEIVED TODAY</div>
            <div style="font-size:20px; font-weight:700; white-space:nowrap;">{{ $inr($paymentsToday) }}</div>
            <div style="font-size:12.5px; font-weight:600; color:#1a1a1a; margin-top:2px;">payments in</div>
        </div>
        <div class="card" style="margin:0;">
            <div style="font-size:11.5px; font-weight:700; letter-spacing:0.5px; color:#1a1a1a;">LAST 7 DAYS</div>
            <div style="font-size:20px; font-weight:700; white-space:nowrap;">{{ $inr($salesWeek) }}</div>
            <div style="font-size:12.5px; font-weight:600; color:#1a1a1a; margin-top:2px;">sales</div>
        </div>
        <div class="card" style="margin:0;">
            <div style="font-size:11.5px; font-weight:700; letter-spacing:0.5px; color:#1a1a1a;">THIS MONTH</div>
            <div style="font-size:20px; font-weight:700; white-space:nowrap;">{{ $inr($salesMonth) }}</div>
            <div style="font-size:12.5px; font-weight:600; margin-top:2px;" class="{{ $delta === null ? '' : ($delta >= 0 ? 'bal-ok' : 'bal-due') }}">
                @if ($delta === null)
                    collected {{ $inr($paymentsMonth) }}
                @else
                    {!! $delta >= 0 ? '&#9650;' : '&#9660;' !!} {{ inr(abs($delta), 1) }}% vs last month
                @endif
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:10px;">
        <div style="font-size:11.5px; font-weight:700; letter-spacing:0.5px; color:#1a1a1a; margin-bottom:6px;">MONEY POSITION</div>
        <div style="display:flex; justify-content:space-between; gap:8px; padding:7px 0; border-bottom:1px solid #999;">
            <span style="font-weight:600;">To receive from {{ $duePartners }} {{ $duePartners === 1 ? 'partner' : 'partners' }}</span>
            <a href="{{ route('partners.index') }}" class="bal-due" style="text-decoration:none; white-space:nowrap;">{{ $inr($receivable) }}</a>
        </div>
        @if ($advanceHeld > 0)
            <div style="display:flex; justify-content:space-between; gap:8px; padding:7px 0; border-bottom:1px solid #999;">
                <span style="font-weight:600;">Advances held</span>
                <span class="bal-adv" style="white-space:nowrap;">{{ $inr($advanceHeld) }}</span>
            </div>
        @endif
        <div style="display:flex; justify-content:space-between; gap:8px; padding:7px 0;">
            <span style="font-weight:600;">To pay {{ $dueSuppliers }} {{ $dueSuppliers === 1 ? 'supplier' : 'suppliers' }}</span>
            <a href="{{ route('suppliers.index') }}" class="bal-due" style="text-decoration:none; white-space:nowrap;">{{ $inr($payable) }}</a>
        </div>
    </div>

    @if ($pendingOrders > 0 || $lowStock > 0 || $negativeStock > 0)
        <div class="card" style="margin-top:10px;">
            <div style="font-size:11.5px; font-weight:700; letter-spacing:0.5px; color:#1a1a1a; margin-bottom:6px;">NEEDS ATTENTION</div>
            @if ($pendingOrders > 0)
                <a href="{{ route('orders.index') }}" style="display:block; text-decoration:none; margin-top:6px; font-size:13.5px; font-weight:600; color:#9a6700; padding:8px 10px; border:1px solid #9a6700; border-left:4px solid #9a6700; background:#fdf8ec; border-radius:4px;">
                    {{ $pendingOrders }} pending retailer {{ $pendingOrders === 1 ? 'order' : 'orders' }} &rarr;
                </a>
            @endif
            @if ($negativeStock > 0)
                <a href="{{ route('products.index') }}" style="display:block; text-decoration:none; margin-top:6px; font-size:13.5px; font-weight:600; color:#b00020; padding:8px 10px; border:1px solid #b00020; border-left:4px solid #b00020; background:#fdf3f3; border-radius:4px;">
                    {{ $negativeStock }} {{ $negativeStock === 1 ? 'product' : 'products' }} in negative stock &rarr;
                </a>
            @endif
            @if ($lowStock > 0)
                <a href="{{ route('products.index') }}" style="display:block; text-decoration:none; margin-top:6px; font-size:13.5px; font-weight:600; color:#9a6700; padding:8px 10px; border:1px solid #9a6700; border-left:4px solid #9a6700; background:#fdf8ec; border-radius:4px;">
                    {{ $lowStock }} {{ $lowStock === 1 ? 'product' : 'products' }} low on stock (&le;10) &rarr;
                </a>
            @endif
        </div>
    @endif

    <div class="card" style="margin-top:10px;">
        <div style="font-size:11.5px; font-weight:700; letter-spacing:0.5px; color:#1a1a1a; margin-bottom:10px;">SALES &mdash; LAST 12 MONTHS</div>
        <div style="display:flex; align-items:flex-end; gap:4px; height:120px;">
            @foreach ($series as $m)
                <div style="flex:1; display:flex; flex-direction:column; justify-content:flex-end; height:100%;" title="{{ $m['ym'] }}: {{ $inr($m['value']) }}">
                    <div style="background:#1a1a1a; border-radius:2px 2px 0 0; height:{{ $m['value'] > 0 ? max(3, round($m['value'] / $maxSeries * 100)) : 0 }}%;"></div>
                </div>
            @endforeach
        </div>
        <div style="display:flex; gap:4px; margin-top:4px;">
            @foreach ($series as $m)
                <div style="flex:1; text-align:center; font-size:9.5px; font-weight:600; color:#1a1a1a;">{{ $m['label'] }}</div>
            @endforeach
        </div>
    </div>
@endsection
