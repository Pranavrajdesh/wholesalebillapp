@extends('layouts.app')

@section('title', 'Import Products')

@section('content')
    <h2>Import Products (CSV)</h2>
    <hr class="rule">
    <p class="crumbs"><a href="{{ route('products.index') }}">&larr; Back to Product List</a></p>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if (session('import_errors') && count(session('import_errors')))
        <div class="error">
            <div style="font-weight:600; margin-bottom:4px;">Skipped rows:</div>
            @foreach (session('import_errors') as $e)
                <div>{{ $e }}</div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <div style="font-weight:600; margin-bottom:6px;">File format</div>
        <div class="muted">Columns: <b>name, brand, category, mrp</b> (required) &mdash; barcode, hsn, tax_percent, tax_inclusive, stock (optional). Column order doesn't matter; extra columns are ignored.</div>
        <div class="muted" style="margin-top:6px;">Existing products are matched by barcode (or by exact name when barcode is empty) and <b>updated</b>, not duplicated. New brands and categories are created automatically. A stock value above 0 turns stock tracking on. tax_inclusive: 1/0 (default 1). Max 2000 rows.</div>
    </div>

    <div style="margin:14px 0 8px;">
        <a class="btn btn-outline" href="{{ route('products.import.sample') }}">&#8681; DOWNLOAD SAMPLE CSV</a>
    </div>

    <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data">
        @csrf

        <label for="file">CSV file</label>
        <input type="file" id="file" name="file" accept=".csv,.txt" required>

        <div style="margin-top:18px;">
            <button class="btn" type="submit">IMPORT</button>
        </div>
    </form>
    <hr class="rule">
@endsection
