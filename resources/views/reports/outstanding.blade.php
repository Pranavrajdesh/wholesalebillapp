@extends('layouts.app')

@section('title', 'Outstanding & Aging')

@section('content')
    <style>
        main.container { max-width: 800px; }
        .agenote { margin: 10px 0 12px; }
        .old1 { color: #9a6700; font-weight: 700; }
        .old2 { color: #b00020; font-weight: 700; }
        .rcards { display: none; }
        @media (max-width: 640px) {
            .rtblwrap { display: none; }
            .rcards { display: block; }
        }
    </style>

    @include('reports._topbar')

    <h2>Outstanding &amp; Aging</h2>
    <hr class="rule">

    <p class="callout agenote">
        Balances as of today. Payments and credit notes settle the oldest invoices first;
        what remains is bucketed by invoice age.
    </p>

    @include('reports._actions', [
        'count_label' => count($rows) . ' ' . (count($rows) === 1 ? 'account' : 'accounts') . ' with balances',
        'csv_url' => route('reports.outstanding') . '?format=csv',
        'pdf_url' => route('reports.outstanding') . '?format=pdf',
        'clear_url' => null,
        'filters_active' => false,
    ])

    <div class="rtblwrap" style="overflow-x:auto;">
        <table class="rtable">
            <thead>
                <tr>
                    <th>Partner</th>
                    <th class="num">Up to 30 days</th>
                    <th class="num">31&ndash;60 days</th>
                    <th class="num">61&ndash;90 days</th>
                    <th class="num">Over 90 days</th>
                    <th class="num" style="width:110px;">Total Due</th>
                    <th class="num">Advance</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td><a href="{{ route('ledger.show', $r->id) }}" style="color:#1a1a1a; font-weight:700;">{{ $r->firm_name }}</a></td>
                        <td class="num">{{ $r->b0 > 0 ? inr($r->b0, 2) : '' }}</td>
                        <td class="num">{{ $r->b31 > 0 ? inr($r->b31, 2) : '' }}</td>
                        <td class="num @if($r->b61 > 0) old1 @endif">{{ $r->b61 > 0 ? inr($r->b61, 2) : '' }}</td>
                        <td class="num @if($r->b91 > 0) old2 @endif">{{ $r->b91 > 0 ? inr($r->b91, 2) : '' }}</td>
                        <td class="num" style="font-weight:700;">{{ $r->due > 0 ? inr($r->due, 2) : '' }}</td>
                        <td class="num">{{ $r->advance > 0 ? inr($r->advance, 2) : '' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="font-weight:600; padding:10px 6px;">Nothing outstanding &mdash; all accounts settled.</td></tr>
                @endforelse
                @if (count($rows))
                    <tr class="tot">
                        <td>Total</td>
                        <td class="num">{{ inr($grand['b0'], 2) }}</td>
                        <td class="num">{{ inr($grand['b31'], 2) }}</td>
                        <td class="num">{{ inr($grand['b61'], 2) }}</td>
                        <td class="num">{{ inr($grand['b91'], 2) }}</td>
                        <td class="num">{{ inr($grand['total'], 2) }}</td>
                        <td class="num">{{ inr($grand['advance'], 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="rcards">
        @foreach ($rows as $r)
            <div class="dcard">
                <div class="dcard-row" style="font-weight:600;">
                    <a href="{{ route('ledger.show', $r->id) }}" style="color:#1a1a1a; font-weight:700;">{{ $r->firm_name }}</a>
                    @if ($r->due > 0)<b class="moneyline" style="color:#b00020;">{{ inr($r->due, 2) }}</b>@else<b class="moneyline" style="color:#9a6700;">Adv {{ inr($r->advance, 2) }}</b>@endif
                </div>
                <div class="dcard-part" style="font-size:12.5px; font-weight:600; color:#1a1a1a;">
                    @if ($r->b0 > 0)<div class="dcard-row"><span>Up to 30 days</span><span class="moneyline">{{ inr($r->b0, 2) }}</span></div>@endif
                    @if ($r->b31 > 0)<div class="dcard-row"><span>31&ndash;60 days</span><span class="moneyline">{{ inr($r->b31, 2) }}</span></div>@endif
                    @if ($r->b61 > 0)<div class="dcard-row old1"><span>61&ndash;90 days</span><span class="moneyline">{{ inr($r->b61, 2) }}</span></div>@endif
                    @if ($r->b91 > 0)<div class="dcard-row old2"><span>Over 90 days</span><span class="moneyline">{{ inr($r->b91, 2) }}</span></div>@endif
                    @if ($r->due > 0 && $r->advance > 0)<div class="dcard-row"><span>Advance held</span><span class="moneyline">{{ inr($r->advance, 2) }}</span></div>@endif
                </div>
            </div>
        @endforeach
        @if (count($rows))
            <div class="dcard" style="border-left:4px solid #b00020; font-weight:700;">
                <div class="dcard-row"><span>Total receivable</span><span class="moneyline" style="color:#b00020;">{{ inr($grand['total'], 2) }}</span></div>
            </div>
        @endif
    </div>

    <div style="margin-top:14px;">
        <a class="btn btn-outline" href="{{ route('reports.index') }}" style="text-decoration:none;">&larr; ALL REPORTS</a>
    </div>
@endsection
