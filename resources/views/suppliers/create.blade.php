@extends('layouts.app')

@section('title', 'Add Supplier')

@section('content')
    <h2>Add Supplier</h2>
    <hr class="rule">

    <form method="POST" action="{{ route('suppliers.store') }}">
        @csrf

        <label for="firm_name">Firm name *</label>
        <input type="text" id="firm_name" name="firm_name" value="{{ old('firm_name') }}" required>
        @error('firm_name')<div class="error">{{ $message }}</div>@enderror

        <label for="contact_name">Contact person</label>
        <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}">

        <label for="mobile">Mobile *</label>
        <input type="tel" id="mobile" name="mobile" inputmode="numeric" maxlength="10" value="{{ old('mobile') }}" required>
        @error('mobile')<div class="error">{{ $message }}</div>@enderror

        <label for="gst_number">GST number</label>
        <input type="text" id="gst_number" name="gst_number" maxlength="15" value="{{ old('gst_number') }}">
        @error('gst_number')<div class="error">{{ $message }}</div>@enderror

        <label for="address">Address</label>
        <input type="text" id="address" name="address" value="{{ old('address') }}">

        <div style="margin-top:18px;">
            <button class="btn" type="submit">SAVE SUPPLIER</button>
        </div>
    </form>

    <div style="margin-top:10px;">
        <a class="btn btn-outline" href="{{ route('suppliers.index') }}">&larr; BACK</a>
    </div>
@endsection
