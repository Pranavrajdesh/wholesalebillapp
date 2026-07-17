@extends('layouts.app')

@section('title', 'Stock Report')

@section('content')
    <style>
        main.container { max-width: 800px; }
        .sumstrip { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 10px; }
        .sumstrip .m { border: 1px solid #1a1a1a; border-radius: 4px; padding: 6px 12px; font-size: 12.5px; font-weight: 600; }
        .stk-neg { color: #b00020; font-weight: 700; }
        .stk-low { color: #9a6700; font-weight: 700; }
        .brandrow td { font-weight: 700; background: #f2f2f2; }
        .rcards { display: none; }
        @media (max-width: 640px) {
            .rtblwrap { display: none; }
            .rcards { display: block; }
        }
    </style>

    @include('reports._topbar')

    <h2>Stock Report</h2>
    <hr class="rule">

    <div class="chip-grid-4" style="margin:10px 0 12px;">
        <a class="btn {{ $filter === 'all' ? '' : 'btn-outline' }}" href="{{ route('reports.stock') }}">ALL</a>
        <a class="btn {{ $filter === 'low' ? '' : 'btn-outline' }}" href="{{ route('reports.stock') }}?filter=low">LOW (&le;10)</a>
        <a class="btn {{ $filter === 'negative' ? '' : 'btn-outline' }}" href="{{ route('reports.stock') }}?filter=negative">NEGATIVE</a>
        <a class="btn {{ $filter === 'out' ? '' : 'btn-outline' }}" href="{{ route('reports.stock') }}?filter=out">OUT</a>
    </div>

    @include('reports._actions', [
        'count_label' => $totals['items'] . ' ' . ($totals['items'] === 1 ? 'product' : 'products'),
        'csv_url' => route('reports.stock') . '?' . http_build_query(array_filter(['filter' => $filter !== 'all' ? $filter : null, 'format' => 'csv'])),
        'pdf_url' => route('reports.stock') . '?' . http_build_query(array_filter(['filter' => $filter !== 'all' ? $filter : null, 'format' => 'pdf'])),
        'clear_url' => route('reports.stock'),
        'filters_active' => $filter !== 'all',
    ])

    <div class="sumstrip">
        <div class="m">Units: {{ inr($totals['units']) }}</div>
        <div class="m">Value at MRP: Rs {{ inr($totals['value'], 2) }}</div>
        @if ($totals['low'] > 0)<div class="m stk-low">Low: {{ $totals['low'] }}</div>@endif
        @if ($totals['negative'] > 0)<div class="m stk-neg">Negative: {{ $totals['negative'] }}</div>@endif
    </div>

    <div class="rtblwrap" style="overflow-x:auto;">
        <table class="rtable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="num" style="width:80px;">Stock</th>
                    <th class="num" style="width:90px;">MRP</th>
                    <th class="num" style="width:120px;">Value at MRP</th>
                </tr>
            </thead>
            <tbody>
                @php $curBrand = null; @endphp
                @forelse ($rows as $r)
                    @if ($r->brand !== $curBrand)
                        @php $curBrand = $r->brand; @endphp
                        <tr class="brandrow"><td colspan="4">{{ $r->brand }}</td></tr>
                    @endif
                    <tr>
                        <td style="font-weight:600;">{{ $r->name }}</td>
                        <td class="num @if($r->stock < 0) stk-neg @elseif($r->stock <= 10) stk-low @endif">{{ $r->stock }}</td>
                        <td class="num">{{ inr($r->mrp, 2) }}</td>
                        <td class="num" style="font-weight:700;">{{ inr($r->value, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="font-weight:600; padding:10px 6px;">No products match this filter.</td></tr>
                @endforelse
                @if ($rows->count())
                    <tr class="tot">
                        <td>Total</td>
                        <td class="num">{{ inr($totals['units']) }}</td>
                        <td></td>
                        <td class="num">{{ inr($totals['value'], 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="rcards">
        @foreach ($rows as $r)
            <div class="dcard">
                <div class="dcard-row" style="font-weight:600;">
                    <span>{{ $r->name }}</span>
                    <b class="moneyline @if($r->stock < 0) stk-neg @elseif($r->stock <= 10) stk-low @endif">{{ $r->stock }}</b>
                </div>
                <div class="dcard-row dcard-part" style="font-size:12.5px; font-weight:600; color:#1a1a1a;">
                    <span>{{ $r->brand }} &middot; MRP {{ inr($r->mrp, 2) }}</span>
                    <span class="moneyline">{{ inr($r->value, 2) }}</span>
                </div>
            </div>
        @endforeach
        @if ($rows->count())
            <div class="dcard" style="border-left:4px solid #1a1a1a; font-weight:700;">
                <div class="dcard-row"><span>{{ inr($totals['units']) }} units</span><span class="moneyline">{{ inr($totals['value'], 2) }}</span></div>
            </div>
        @endif
    </div>

    <div style="margin-top:14px;">
        <a class="btn btn-outline" href="{{ route('reports.index') }}" style="text-decoration:none;">&larr; ALL REPORTS</a>
    </div>
@endsection
