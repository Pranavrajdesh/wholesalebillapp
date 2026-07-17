@extends('layouts.app')

@section('title', 'Inward Entry')

@section('content')
    <h2 style="font-size:20px; margin:6px 0 10px;">Inward Entry #{{ $entry->id }}</h2>
    <hr class="rule">

    <div class="card">
        <div style="font-weight:700;">{{ $entry->inward_date->format('d M Y') }}</div>
        @if ($entry->supplier)
            <div style="color:#444; font-size:13.5px;">Supplier: <b>{{ $entry->supplier->firm_name }}</b> &middot; {{ $entry->supplier->mobile }}</div>
        @endif
    </div>

    @if ($entry->note)
        <div style="margin-top:10px; font-size:13.5px; font-weight:600; color:#1a1a1a; padding:8px 10px; border:1px solid #999; border-left:4px solid #1a1a1a; background:#f7f7f7; border-radius:4px;">NOTE: {{ $entry->note }}</div>
    @endif

    @php $curBrand = null; @endphp
    @foreach ($entry->lines as $line)
        @if ($line->brand !== $curBrand)
            @php $curBrand = $line->brand; @endphp
            <div class="bghead">{{ $line->brand }}</div>
        @endif
        <div style="border:1px solid #1a1a1a; border-radius:4px; padding:8px 10px; margin:8px 0; font-size:13.5px;">
            <div style="display:flex; justify-content:space-between; gap:8px;">
                <span style="font-weight:700;">{{ $line->name }}</span>
                <b style="white-space:nowrap;">+ {{ $line->qty }}</b>
            </div>
            <div style="display:flex; justify-content:space-between; gap:8px; margin-top:6px; padding-top:6px; border-top:1px dashed #999; font-size:12.5px;">
                <span style="color:#1a1a1a; font-weight:600;">
                    @if ($line->purchase_rate)
                        Rate &#8377;{{ number_format($line->purchase_rate, 2) }} &middot; Value <b>&#8377;{{ number_format($line->qty * $line->purchase_rate, 2) }}</b>
                    @else
                        Rate &mdash;
                    @endif
                </span>
                <span style="color:#1a1a1a; font-weight:700; white-space:nowrap;">In hand: {{ (int) ($line->product->stock_qty ?? 0) }}</span>
            </div>
        </div>
    @endforeach

    <div class="card" style="margin-top:14px;">
        <div class="sumrow total"><span>TOTAL UNITS IN</span><span>{{ $entry->lines->sum('qty') }}</span></div>
        <div class="sumrow"><span>Current stock in hand (these items)</span><span><b>{{ (int) $entry->lines->sum(fn ($l) => (int) ($l->product->stock_qty ?? 0)) }}</b></span></div>
    </div>

    <div style="margin-top:14px; display:flex; gap:8px; flex-wrap:wrap;">
        <a class="btn" href="{{ route('inward.create') }}">+ NEW INWARD ENTRY</a>
        <a class="btn btn-outline" href="{{ route('inward.index') }}">&larr; ALL ENTRIES</a>
    </div>
@endsection
