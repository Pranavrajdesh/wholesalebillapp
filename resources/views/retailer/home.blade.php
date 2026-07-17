@extends('layouts.app')

@section('title', 'Retailer Home')

@section('content')
    <h2>Welcome, {{ auth('partner')->user()->firm_name }}</h2>
    <hr class="rule">

    <div class="card">
        <div style="font-weight:600; margin-bottom:6px;">Your catalogue is coming soon</div>
        <div class="muted">Browsing products and placing orders will be available here shortly. Contact your wholesaler for anything urgent.</div>
    </div>
@endsection