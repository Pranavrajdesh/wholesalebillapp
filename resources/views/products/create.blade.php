@extends('layouts.app')

@section('title', 'New Product')

@section('content')
    <h2>New Product</h2>
    <hr class="rule">
    <p class="crumbs"><a href="{{ route('products.index') }}">&larr; Back to Product List</a></p>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf

        <label for="name">Product name</label>
        <div class="inputrow">
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            <button type="button" class="copybtn" title="Copy">&#10697;</button>
        </div>

        <label for="brand_id">Brand</label>
        <select id="brand_id" name="brand_id" required>
            <option value="">&mdash; select brand &mdash;</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">&mdash; select category &mdash;</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>

        <label for="barcode">Barcode</label>
        <div class="inputrow">
            <input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}">
            <button type="button" class="copybtn" title="Copy">&#10697;</button>
        </div>

        <label for="mrp">MRP (&#8377;)</label>
        <input type="number" id="mrp" name="mrp" step="0.01" min="0" value="{{ old('mrp') }}" required>

        <label for="hsn_code">HSN code</label>
        <input type="text" id="hsn_code" name="hsn_code" value="{{ old('hsn_code') }}">

        <label for="tax_percent">Tax %</label>
        <input type="number" id="tax_percent" name="tax_percent" step="0.01" min="0" max="100" value="{{ old('tax_percent', '18') }}" required>

        <input type="hidden" name="tax_inclusive" value="0">
        <div class="check">
            <input type="checkbox" id="tax_inclusive" name="tax_inclusive" value="1" @checked(old('tax_inclusive', '1') == '1')>
            <label for="tax_inclusive" style="margin:0;">Rate is tax inclusive</label>
        </div>

        <input type="hidden" name="track_stock" value="1">

        <input type="hidden" name="is_visible" value="0">
        <div class="check">
            <input type="checkbox" id="is_visible" name="is_visible" value="1" @checked(old('is_visible', '1') == '1')>
            <label for="is_visible" style="margin:0;">Visible in retailer catalogue</label>
        </div>

        <input type="hidden" name="rate_visible" value="0">
        <div class="check">
            <input type="checkbox" id="rate_visible" name="rate_visible" value="1" @checked(old('rate_visible', '1') == '1')>
            <label for="rate_visible" style="margin:0;">Rates visible to retailers (untick: MRP only, you can still bill with saved rates)</label>
        </div>

        <label for="stock_qty">Opening stock qty</label>
        <input type="number" id="stock_qty" name="stock_qty" min="0" value="{{ old('stock_qty', 0) }}">

        <label for="image">Product image</label>
        <input type="file" id="image" name="image" accept="image/*">

        <div style="margin-top:18px;">
            <button class="btn" type="submit">SAVE PRODUCT</button>
        </div>
    </form>
    <hr class="rule">
@endsection
