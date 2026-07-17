@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <h2>Products</h2>

    <div class="searchwrap">
        <input type="text" id="search" placeholder="Search name or barcode" autocomplete="off">
        <button type="button" id="clearsearch" class="xbtn" hidden>&times;</button>
        <div id="suggestions" class="suggest" hidden></div>
    </div>

    <details class="filterbox">
        <summary>FILTERS</summary>
        <label for="f_brand">Brand</label>
    <select id="f_brand">
        <option value="">All brands</option>
        @foreach ($brands as $b)
            <option value="{{ $b->id }}">{{ $b->name }}</option>
        @endforeach
    </select>

    <label for="f_category">Category</label>
    <select id="f_category">
        <option value="">All categories</option>
        @foreach ($categories as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
    </select>

    <label for="f_sort">Sort</label>
    <select id="f_sort">
        <option value="name_asc">Name A&ndash;Z</option>
        <option value="name_desc">Name Z&ndash;A</option>
        <option value="mrp_asc">MRP low &rarr; high</option>
        <option value="mrp_desc">MRP high &rarr; low</option>
    </select>

    <div style="margin:14px 0 8px;">
        <button type="button" id="clearfilters" class="btn btn-outline">&#10005; CLEAR FILTERS</button>
    </div>

    <div style="margin:8px 0 14px;">
        <a class="btn" href="{{ route('products.create') }}">+ NEW PRODUCT</a>
    </div>
    <div style="margin:8px 0 14px;">
        <a class="btn btn-outline" href="{{ route('products.import.form') }}">&#8682; IMPORT CSV</a>
    </div>

    </details>

    <hr class="rule">
    <p id="count" class="count"></p>
    <div id="list"></div>
    <p id="loading" class="muted" hidden>Loading&hellip;</p>
    <p id="empty" class="muted" hidden>No products found.</p>
    <button id="loadmore" class="btn btn-outline" hidden>LOAD MORE</button>

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const dataUrl = "{{ route('products.data') }}";
        const state = { q: '', brand_id: '', category_id: '', sort: 'name_asc', offset: 0 };

        const list = document.getElementById('list');
        const empty = document.getElementById('empty');
        const loading = document.getElementById('loading');
        const loadmore = document.getElementById('loadmore');
        const search = document.getElementById('search');
        const clearsearch = document.getElementById('clearsearch');
        const sug = document.getElementById('suggestions');
        const count = document.getElementById('count');
        const brandSel = document.getElementById('f_brand');
        const catSel = document.getElementById('f_category');
        const sortSel = document.getElementById('f_sort');

        const brandCombo = UI.comboSelect(brandSel, { placeholder: 'All brands' });
        const catCombo = UI.comboSelect(catSel, { placeholder: 'All categories' });
        const nav = UI.listKeyNav(search, sug, '.sug-item');
        UI.initGoTop();

        let sugTimer = null;

        function esc(s) {
            const d = document.createElement('div');
            d.textContent = s ?? '';
            return d.innerHTML;
        }

        function params(extra) {
            const p = new URLSearchParams();
            if (state.q) p.set('q', state.q);
            if (state.brand_id) p.set('brand_id', state.brand_id);
            if (state.category_id) p.set('category_id', state.category_id);
            p.set('sort', state.sort);
            p.set('status', 'all');
            Object.entries(extra || {}).forEach(([k, v]) => p.set(k, v));
            return p.toString();
        }

        function imgHtml(item, cls) {
            return item.image_url
                ? '<img class="' + cls + '" src="' + item.image_url + '" alt="">'
                : '<div class="' + cls + '">' + esc(item.initials) + '</div>';
        }

        function cardHtml(item) {
            return '<div class="card"><div class="prow">'
                + imgHtml(item, 'pimg')
                + '<div>'
                + '<div style="font-weight:600;">' + esc(item.name) + '</div>'
                + '<div class="muted">' + esc(item.brand) + ' &middot; ' + esc(item.category) + '</div>'
                + '<div class="muted">MRP &#8377;' + item.mrp.toFixed(2)
                + (item.barcode ? ' &middot; ' + esc(item.barcode) : '') + '</div>'
                + (item.track_stock ? '<div class="muted">Stock: ' + item.stock_qty + '</div>' : '')
                + (item.is_active ? '' : '<div class="badge-inactive">INACTIVE</div>')
                + '<div style="margin-top:6px;"><a class="editlink" href="' + item.edit_url + '">EDIT</a> &nbsp;&middot;&nbsp; <a class="editlink" href="' + item.slabs_url + '">RATES</a></div>'
                + '</div></div></div>';
        }

        async function fetchList(reset) {
            if (reset) { state.offset = 0; list.innerHTML = ''; }
            loading.hidden = false;
            empty.hidden = true;
            const res = await fetch(dataUrl + '?' + params({ offset: state.offset, limit: 25 }));
            const data = await res.json();
            loading.hidden = true;
            data.items.forEach(i => list.insertAdjacentHTML('beforeend', cardHtml(i)));
            state.offset = data.next_offset;
            loadmore.hidden = !data.has_more;
            empty.hidden = list.children.length > 0;
            count.textContent = data.total + (data.total === 1 ? ' product' : ' products');
        }

        function pickSug(item) {
            search.value = item.dataset.name;
            state.q = item.dataset.name;
            sug.hidden = true;
            nav.reset();
            clearsearch.hidden = false;
            fetchList(true);
        }

        search.addEventListener('input', () => {
            clearTimeout(sugTimer);
            clearsearch.hidden = search.value === '';
            const term = search.value.trim();
            if (!term) {
                sug.hidden = true;
                nav.reset();
                state.q = '';
                fetchList(true);
                return;
            }
            sugTimer = setTimeout(async () => {
                const res = await fetch(dataUrl + '?' + new URLSearchParams({ q: term, limit: 6, status: 'all' }));
                const data = await res.json();
                if (!data.items.length) { sug.hidden = true; nav.reset(); return; }
                sug.innerHTML = data.items.map(i =>
                    '<div class="sug-item" data-name="' + esc(i.name) + '">'
                    + imgHtml(i, 'sug-img')
                    + '<div><div>' + esc(i.name) + '</div>'
                    + '<div class="muted">MRP &#8377;' + i.mrp.toFixed(2) + '</div></div>'
                    + '</div>'
                ).join('');
                sug.hidden = false;
                nav.reset();
            }, 250);
        });

        search.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const active = nav.active();
                if (active) { pickSug(active); return; }
                state.q = search.value.trim();
                sug.hidden = true;
                fetchList(true);
            }
            if (e.key === 'Escape') sug.hidden = true;
        });

        sug.addEventListener('click', e => {
            const item = e.target.closest('.sug-item');
            if (item) pickSug(item);
        });

        document.addEventListener('click', e => {
            if (!e.target.closest('.searchwrap')) sug.hidden = true;
        });

        clearsearch.addEventListener('click', () => {
            search.value = '';
            clearsearch.hidden = true;
            sug.hidden = true;
            nav.reset();
            state.q = '';
            fetchList(true);
            search.focus();
        });

        document.getElementById('clearfilters').addEventListener('click', () => {
            state.q = '';
            state.brand_id = '';
            state.category_id = '';
            state.sort = 'name_asc';
            search.value = '';
            clearsearch.hidden = true;
            sug.hidden = true;
            brandSel.value = '';
            catSel.value = '';
            sortSel.value = 'name_asc';
            brandCombo.syncLabel();
            catCombo.syncLabel();
            fetchList(true);
        });

        brandSel.addEventListener('change', () => {
            state.brand_id = brandSel.value;
            fetchList(true);
        });

        catSel.addEventListener('change', () => {
            state.category_id = catSel.value;
            fetchList(true);
        });

        sortSel.addEventListener('change', () => {
            state.sort = sortSel.value;
            fetchList(true);
        });

        loadmore.addEventListener('click', () => fetchList(false));

        fetchList(true);
    });
    </script>
@endsection
