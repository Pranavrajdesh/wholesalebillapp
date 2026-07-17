@extends('layouts.app')

@section('title', 'Sales Summary')

@section('content')
    <style>main.container { max-width: 800px; }</style>
    @include('reports._topbar')
    <h2>Sales Summary</h2>
    <hr class="rule">

    @php
        $inr = fn ($n) => 'Rs ' . inr($n, 2);
        $base = fn (array $extra) => route('reports.sales_summary') . '?' . http_build_query(array_filter(array_merge(
            ['group' => $group, 'from' => $from, 'to' => $to], $extra
        )));
        $isLine = in_array($group, ['brand', 'product'], true);
    @endphp

    <form method="GET" action="{{ route('reports.sales_summary') }}">
        <input type="hidden" name="group" value="{{ $group }}">
        <div style="border:1.5px solid #1a1a1a; border-radius:4px; margin:10px 0 14px;">
            <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:flex-end; padding:10px 12px; border-bottom:1px solid #999;">
                <div style="display:flex; flex-direction:column; font-size:11px; color:#555; gap:3px;">
                    <span>From</span><input type="date" name="from" value="{{ $from }}" style="padding:8px; border:1px solid #aaa; border-radius:4px;">
                </div>
                <div style="display:flex; flex-direction:column; font-size:11px; color:#555; gap:3px;">
                    <span>To</span><input type="date" name="to" value="{{ $to }}" style="padding:8px; border:1px solid #aaa; border-radius:4px;">
                </div>
                <button class="btn" type="submit" style="padding:9px 18px;">APPLY</button>
            </div>
            <div class="chip-grid-4" style="padding:10px 12px; border-bottom:1px solid #999;">
                <a class="btn {{ ($fyActive ?? '') === 'thismonth' ? '' : 'btn-outline' }}" href="{{ $base(['from' => null, 'to' => null]) }}&fy=thismonth">THIS MONTH</a>
                <a class="btn {{ ($fyActive ?? '') === 'this' ? '' : 'btn-outline' }}" href="{{ $base(['from' => null, 'to' => null]) }}&fy=this">THIS FY</a>
                <a class="btn {{ ($fyActive ?? '') === 'last' ? '' : 'btn-outline' }}" href="{{ $base(['from' => null, 'to' => null]) }}&fy=last">LAST FY</a>
                <a class="btn {{ ($fyActive ?? '') === 'all' ? '' : 'btn-outline' }}" href="{{ $base(['from' => null, 'to' => null]) }}">ALL</a>
            </div>
            <div class="chip-grid-4" style="padding:10px 12px;">
                @foreach (['month' => 'BY MONTH', 'partner' => 'BY PARTNER', 'brand' => 'BY BRAND', 'product' => 'BY PRODUCT'] as $g => $label)
                    <a href="{{ route('reports.sales_summary') }}?{{ http_build_query(array_filter(['group' => $g, 'from' => $from, 'to' => $to])) }}"
                       class="btn {{ $group === $g ? '' : 'btn-outline' }}" style="padding:9px 4px; font-size:11px; text-decoration:none; text-align:center;">{{ $label }}</a>
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
        <p style="font-size:12.5px; font-weight:600; color:#1a1a1a; margin:0 0 8px; padding:8px 10px; border:1px solid #999; border-left:4px solid #1a1a1a; background:#f7f7f7; border-radius:4px;">
            Amounts are gross line values (before invoice-level discount and round-off).
        </p>
    @endif

    <div class="rtblwrap" style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:12.5px; border:1.5px solid #1a1a1a;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:6px; border:1px solid #555; border-top:1.5px solid #1a1a1a; border-bottom:1.5px solid #1a1a1a; background:#f2f2f2; font-size:11.5px;">{{ ucfirst($group) }}</th>
                    @if ($isLine)
                        <th style="text-align:right; padding:6px; border:1px solid #555; border-top:1.5px solid #1a1a1a; border-bottom:1.5px solid #1a1a1a; background:#f2f2f2; font-size:11.5px;">Qty</th>
                        <th style="text-align:right; padding:6px; border:1px solid #555; border-top:1.5px solid #1a1a1a; border-bottom:1.5px solid #1a1a1a; background:#f2f2f2; font-size:11.5px;">Free</th>
                    @else
                        <th style="text-align:right; padding:6px; border:1px solid #555; border-top:1.5px solid #1a1a1a; border-bottom:1.5px solid #1a1a1a; background:#f2f2f2; font-size:11.5px;">Bills</th>
                    @endif
                    <th style="text-align:right; padding:6px; border:1px solid #555; border-top:1.5px solid #1a1a1a; border-bottom:1.5px solid #1a1a1a; background:#f2f2f2; font-size:11.5px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td style="padding:5px 6px; border:1px solid #999; font-weight:600;">{{ $r->g }}</td>
                        @if ($isLine)
                            <td style="padding:5px 6px; border:1px solid #999; text-align:right;">{{ $r->qty }}</td>
                            <td style="padding:5px 6px; border:1px solid #999; text-align:right;">{{ $r->free > 0 ? $r->free : '' }}</td>
                        @else
                            <td style="padding:5px 6px; border:1px solid #999; text-align:right;">{{ $r->bills }}</td>
                        @endif
                        <td style="padding:5px 6px; border:1px solid #999; text-align:right; white-space:nowrap; font-weight:700;">{{ inr($r->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="{{ $isLine ? 4 : 3 }}" style="padding:10px 6px; border:1px solid #999; font-weight:600;">No sales in this period.</td></tr>
                @endforelse
                @if ($rows->count())
                    <tr>
                        <td colspan="{{ $isLine ? 3 : 2 }}" style="padding:6px; border:1px solid #999; border-top:1.5px solid #1a1a1a; font-weight:700; background:#f7f7f7;">Total</td>
                        <td style="padding:6px; border:1px solid #999; border-top:1.5px solid #1a1a1a; text-align:right; font-weight:700; background:#f7f7f7; white-space:nowrap;">{{ inr($totalAmount, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="rcards">
        @foreach ($rows as $r)
            <div class="dcard">
                <div style="display:flex; justify-content:space-between; gap:8px;">
                    <span style="font-weight:700;">{{ $r->g }}</span>
                    <b style="white-space:nowrap;">{{ inr($r->amount, 2) }}</b>
                </div>
                <div style="margin-top:6px; padding-top:6px; border-top:1px dashed #999; font-size:12.5px; font-weight:600; color:#1a1a1a;">
                    @if ($r->bills !== null) {{ $r->bills }} {{ $r->bills == 1 ? 'bill' : 'bills' }} @else Qty {{ $r->qty }}@if ($r->free > 0) + {{ $r->free }} free @endif @endif
                </div>
            </div>
        @endforeach
        @if ($rows->count())
            <div class="dcard" style="border-left:4px solid #1a1a1a; font-weight:700; display:flex; justify-content:space-between;">
                <span>Total</span><span style="white-space:nowrap;">{{ inr($totalAmount, 2) }}</span>
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
