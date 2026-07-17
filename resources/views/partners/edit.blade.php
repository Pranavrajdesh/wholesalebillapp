@extends('layouts.app')

@section('title', 'Edit Partner')

@section('content')
    <h2>Edit Partner</h2>
    <hr class="rule">
    <p class="crumbs"><a href="{{ route('partners.index') }}">&larr; Back to Partner List</a></p>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('partners.update', $partner) }}">
        @csrf
        @method('PUT')

        <label for="firm_name">Firm name</label>
        <div class="inputrow">
            <input type="text" id="firm_name" name="firm_name" value="{{ old('firm_name', $partner->firm_name) }}" required>
            <button type="button" class="copybtn" title="Copy">&#10697;</button>
        </div>

        <label for="contact_name">Contact person</label>
        <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name', $partner->contact_name) }}" placeholder="Optional">

        <label for="mobile">Mobile (login number)</label>
        <div class="inputrow">
            <input type="tel" id="mobile" name="mobile" inputmode="numeric" maxlength="10" value="{{ old('mobile', $partner->mobile) }}" required>
            <button type="button" class="copybtn" title="Copy">&#10697;</button>
        </div>
        <label for="gst_number">GST number</label>
        <div class="inputrow">
            <input type="text" id="gst_number" name="gst_number" maxlength="15" value="{{ old('gst_number', $partner->gst_number) }}" placeholder="Optional">
            <button type="button" class="copybtn" title="Copy">&#10697;</button>
        </div>

        <label for="alt_mobile">Alternate mobile</label>
        <input type="tel" id="alt_mobile" name="alt_mobile" inputmode="numeric" maxlength="10" value="{{ old('alt_mobile', $partner->alt_mobile) }}" placeholder="Optional">

        <label for="address">Address</label>
        <input type="text" id="address" name="address" maxlength="500" value="{{ old('address', $partner->address) }}" placeholder="Optional">

        <input type="hidden" name="portal_access" value="0">
        <div class="check">
            <input type="checkbox" id="portal_access" name="portal_access" value="1" @checked(old('portal_access', $partner->portal_access ? '1' : '0') == '1')>
            <label for="portal_access" style="margin:0;">Portal access (can log in and place orders)</label>
        </div>

        <input type="hidden" name="show_prices" value="0">
        <div class="check">
            <input type="checkbox" id="show_prices" name="show_prices" value="1" @checked(old('show_prices', $partner->show_prices ? '1' : '0') == '1')>
            <label for="show_prices" style="margin:0;">Show prices (untick to show catalogue without rates)</label>
        </div>

        <input type="hidden" name="is_active" value="0">
        <div class="check">
            <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $partner->is_active ? '1' : '0') == '1')>
            <label for="is_active" style="margin:0;">Active (untick to disable this partner)</label>
        </div>

        <div style="margin-top:18px;">
            <button class="btn" type="submit">SAVE CHANGES</button>
        </div>
    </form>
    <hr class="rule">
@endsection
