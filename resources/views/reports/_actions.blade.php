{{-- Shared report action bar.
     Params: $count_label (string), $csv_url, $pdf_url, $clear_url (nullable), $filters_active (bool) --}}
<div class="ractions">
    <div class="stitle" style="align-self:center;">{{ $count_label }}</div>
    <div class="ractions-btns {{ ($filters_active ?? false) ? 'three' : '' }}">
        <a class="btn" href="{{ $csv_url }}">CSV</a>
        <a class="btn" href="{{ $pdf_url }}">PDF</a>
        @if (($filters_active ?? false) && !empty($clear_url))
            <a class="btn btn-outline" href="{{ $clear_url }}">CLEAR FILTER</a>
        @endif
    </div>
</div>
