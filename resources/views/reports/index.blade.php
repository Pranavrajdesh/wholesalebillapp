@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <style>main.container { max-width: 800px; }</style>
    <h2>Reports</h2>
    <hr class="rule">

    @php
        $live = [
            ['Sales Register', 'Invoice-wise listing for any period, with totals and CSV download.', route('reports.sales_register')],
            ['Sales Summary', 'Sales grouped by month, partner, brand, or product.', route('reports.sales_summary')],
            ['Collections Register', 'Payments received, method-wise totals.', route('reports.collections')],
            ['Outstanding & Aging', 'Partner dues bucketed by age, oldest-first settlement.', route('reports.outstanding')],
            ['Stock Report', 'Current stock with MRP values and low-stock flags.', route('reports.stock')],
            ['Purchase Register', 'Supplier bills and stock inward, period-wise.', route('reports.purchases')],
            ['GST / HSN Summary', 'HSN-wise taxable and tax for a period.', route('reports.gst')],
            ['Collections Register', 'Payments received, method-wise totals.', route('reports.collections')],
            ['Outstanding & Aging', 'Partner dues bucketed by age, oldest-first settlement.', route('reports.outstanding')],
            ['Stock Report', 'Current stock with MRP values and low-stock flags.', route('reports.stock')],
            ['Purchase Register', 'Supplier bills and stock inward, period-wise.', route('reports.purchases')],
            ['GST / HSN Summary', 'HSN-wise taxable and tax for a period.', route('reports.gst')],
        ];
        $soon = [];
    @endphp

    @foreach ($live as [$title, $desc, $url])
        <div class="card" style="margin-bottom:10px;">
            <div style="font-weight:700; font-size:15px;">{{ $title }}</div>
            <div style="font-size:13px; color:#444; margin-top:2px;">{{ $desc }}</div>
            <div style="margin-top:10px;">
                <a class="btn cardbtn" href="{{ $url }}" style="text-decoration:none;">OPEN</a>
            </div>
        </div>
    @endforeach

    @foreach ($soon as [$title, $desc])
        <div class="card" style="margin-bottom:10px;">
            <div style="display:flex; justify-content:space-between; gap:8px; align-items:flex-start;">
                <div style="font-weight:700; font-size:15px;">{!! $title !!}</div>
                <span class="badge-amber">COMING SOON</span>
            </div>
            <div style="font-size:13px; color:#444; margin-top:2px;">{{ $desc }}</div>
        </div>
    @endforeach
@endsection
