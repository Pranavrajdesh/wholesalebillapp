@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <h2>Manage App</h2>
    <hr class="rule">

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin:10px 0 14px;">
        <a href="#firm" class="btn" style="padding:11px 8px; font-size:12px; text-decoration:none; text-align:center;">FIRM</a>
        <a href="#bank" class="btn" style="padding:11px 8px; font-size:12px; text-decoration:none; text-align:center;">BANK &amp; UPI</a>
        <a href="#print" class="btn" style="padding:11px 8px; font-size:12px; text-decoration:none; text-align:center;">PRINT</a>
        <a href="#billing" class="btn" style="padding:11px 8px; font-size:12px; text-decoration:none; text-align:center;">BILLING</a>
    </div>
    <style>.card[id] { scroll-margin-top: 90px; }</style>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')

        <div class="card" id="firm">
            <div style="font-weight:700; margin-bottom:2px;">Firm details</div>
            <div class="muted" style="font-size:12px;">Printed at the top of every invoice.</div>

            <label for="firm_name">Firm name</label>
            <input type="text" id="firm_name" name="firm_name" value="{{ old('firm_name', $s['firm_name'] ?? '') }}" required>

            <label for="firm_gst">GST number</label>
            <div class="inputrow">
                <input type="text" id="firm_gst" name="firm_gst" maxlength="15" value="{{ old('firm_gst', $s['firm_gst'] ?? '') }}" placeholder="Optional">
                <button type="button" class="copybtn" title="Copy">&#10697;</button>
            </div>

            <label for="firm_mobile">Mobile</label>
            <div class="inputrow">
                <input type="tel" id="firm_mobile" name="firm_mobile" inputmode="numeric" maxlength="10" value="{{ old('firm_mobile', $s['firm_mobile'] ?? '') }}" required>
                <button type="button" class="copybtn" title="Copy">&#10697;</button>
            </div>

            <label for="firm_alt_mobile">Alternate mobile</label>
            <input type="tel" id="firm_alt_mobile" name="firm_alt_mobile" inputmode="numeric" maxlength="10" value="{{ old('firm_alt_mobile', $s['firm_alt_mobile'] ?? '') }}" placeholder="Optional">

            <label for="firm_address">Address</label>
            <input type="text" id="firm_address" name="firm_address" maxlength="500" value="{{ old('firm_address', $s['firm_address'] ?? '') }}" placeholder="Optional">
        </div>

        <div class="card" id="bank">
            <div style="font-weight:700; margin-bottom:2px;">Bank details</div>
            <div class="muted" style="font-size:12px;">Printed on invoices for payment transfer.</div>

            <label for="bank_name">Bank name</label>
            <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $s['bank_name'] ?? '') }}" placeholder="Optional">

            <label for="bank_account">Account number</label>
            <div class="inputrow">
                <input type="text" id="bank_account" name="bank_account" maxlength="30" value="{{ old('bank_account', $s['bank_account'] ?? '') }}" placeholder="Optional">
                <button type="button" class="copybtn" title="Copy">&#10697;</button>
            </div>

            <label for="bank_ifsc">IFSC code</label>
            <div class="inputrow">
                <input type="text" id="bank_ifsc" name="bank_ifsc" maxlength="15" value="{{ old('bank_ifsc', $s['bank_ifsc'] ?? '') }}" placeholder="Optional">
                <button type="button" class="copybtn" title="Copy">&#10697;</button>
            </div>

            <label for="bank_holder">Account holder name</label>
            <input type="text" id="bank_holder" name="bank_holder" value="{{ old('bank_holder', $s['bank_holder'] ?? '') }}" placeholder="Optional">
        </div>

        <div class="card">
            <div style="font-weight:700; margin-bottom:2px;">UPI</div>
            <div class="muted" style="font-size:12px;">A scan-to-pay QR with the bill amount is printed on invoices when this is set.</div>

            <label for="upi_id">UPI ID</label>
            <div class="inputrow">
                <input type="text" id="upi_id" name="upi_id" maxlength="100" value="{{ old('upi_id', $s['upi_id'] ?? '') }}" placeholder="e.g. 9999999999@upi (optional)">
                <button type="button" class="copybtn" title="Copy">&#10697;</button>
            </div>
        </div>

        <div class="card" id="print">
            <div style="font-weight:700; margin-bottom:2px;">Invoice print options</div>
            <div class="muted" style="font-size:12px;">Choose what appears on printed invoices.</div>

            <input type="hidden" name="print_payment" value="0">
            <div class="check">
                <input type="checkbox" id="print_payment" name="print_payment" value="1" @checked(old('print_payment', $s['print_payment'] ?? '1') == '1')>
                <label for="print_payment" style="margin:0;">Include payment details &amp; UPI QR</label>
            </div>

            <input type="hidden" name="print_projection" value="0">
            <div class="check">
                <input type="checkbox" id="print_projection" name="print_projection" value="1" @checked(old('print_projection', $s['print_projection'] ?? '1') == '1')>
                <label for="print_projection" style="margin:0;">Include projected profit block</label>
            </div>
        </div>

        <div class="card" id="billing">
            <div style="font-weight:700; margin-bottom:2px;">Billing options</div>

            <input type="hidden" name="allow_negative_stock" value="0">
            <div class="check">
                <input type="checkbox" id="allow_negative_stock" name="allow_negative_stock" value="1" @checked(old('allow_negative_stock', $s['allow_negative_stock'] ?? '1') == '1')>
                <label for="allow_negative_stock" style="margin:0;">Allow negative stock (ticked: bills always save, stock can go below zero &mdash; unticked: bills exceeding available stock are blocked)</label>
            </div>
        </div>

        <button class="btn" type="submit">SAVE SETTINGS</button>
    </form>
    <hr class="rule">
@endsection
