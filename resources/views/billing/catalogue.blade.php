@extends('layouts.app')

@section('title', 'Catalogue')

@section('content')
    <div id="partnerstrip" class="card pstrip">
        <div>
            <div class="muted" style="font-size:12px;">BILLING TO</div>
            <div id="pstripname" style="font-weight:700;"></div>
        </div>
        <a href="{{ route('billing.select') }}" class="editlink">CHANGE</a>
    </div>

    <div class="searchwrap">
        <input type="text" id="search" placeholder="Scan barcode or search product" autocomplete="off">
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
    </details>

    <hr class="rule">
    <p id="count" class="count"></p>

    <div id="list"></div>
    <p id="loading" class="muted" hidden>Loading&hellip;</p>
    <p id="empty" class="muted" hidden>No products found.</p>
    <button id="loadmore" class="btn btn-outline" hidden>LOAD MORE</button>

    <div style="height:74px;"></div>

    <div id="cartbar" class="cartbar">
        <div class="inner">
            <div>
                <div id="cartcount" style="font-weight:700;">0 items</div>
                <div id="carttotal" class="cartbar-total">&#8377;0.00</div>
            </div>
            <button type="button" id="gocart" class="cartbar-btn">CART &rarr;</button>
        </div>
    </div>

    @include('billing._composer')

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const B = window.Billing;
        const CART_KEY = 'wba_cart';
        const dataUrl = "{{ route('products.data') }}";
        const selectUrl = "{{ route('billing.select') }}";

        let cart = null;
        try { cart = JSON.parse(sessionStorage.getItem(CART_KEY)); } catch (e) { cart = null; }
        if (!cart || !cart.partner) { window.location.href = selectUrl; return; }
        cart.lines = cart.lines || [];

        document.getElementById('pstripname').textContent = cart.partner.firm_name;

        const state = { q: '', brand_id: '', category_id: '', sort: 'name_asc', offset: 0 };
        const byId = {};
        const list = document.getElementById('list');
        const loading = document.getElementById('loading');
        const empty = document.getElementById('empty');
        const loadmore = document.getElementById('loadmore');
        const count = document.getElementById('count');
        const brandSel = document.getElementById('f_brand');
        const catSel = document.getElementById('f_category');
        const sortSel = document.getElementById('f_sort');

        UI.comboSelect(brandSel, { placeholder: 'All brands' });
        UI.comboSelect(catSel, { placeholder: 'All categories' });
        UI.initGoTop();

        const saveCart = () => sessionStorage.setItem(CART_KEY, JSON.stringify(cart));
        const getLine = id => cart.lines.find(l => l.product_id === id);

        const composer = B.createComposer({
            getCart: () => cart,
            saveCart,
            onChange: id => { renderLineState(id); renderCartBar(); },
        });

        B.createProductSearch({
            input: document.getElementById('search'),
            clearBtn: document.getElementById('clearsearch'),
            sugEl: document.getElementById('suggestions'),
            dataUrl,
            onPick: item => { byId[item.id] = item; composer.open(item); },
            onQuery: q => { state.q = q; fetchList(true); },
        });

        function cardHtml(item) {
            const line = getLine(item.id);
            const qty = line ? line.qty : 0;
            const billable = item.slabs && item.slabs.length;
            return '<div class="card pcard" data-id="' + item.id + '">'
                + '<div class="prow">'
                + (item.image_url
                    ? '<img class="pimg" src="' + item.image_url + '" alt="">'
                    : '<div class="pimg">' + B.esc(item.initials) + '</div>')
                + '<div>'
                + '<div style="font-weight:600;">' + B.esc(item.name) + '</div>'
                + '<div class="muted">' + B.esc(item.brand) + ' &middot; ' + B.esc(item.category) + '</div>'
                + '<div class="mrpline">MRP <b>' + B.money(item.mrp) + '</b>'
                + (item.track_stock ? '<span class="muted"> &middot; Stock: ' + item.stock_qty + '</span>' : '') + '</div>'
                + '</div></div>'
                + '<div class="muted" style="margin-top:10px; font-size:12px; font-weight:600;">RATE BY QUANTITY</div>'
                + B.slabTableHtml(item)
                + '<div class="linehint muted"></div>'
                + (billable
                    ? '<div class="stepper">'
                        + '<button type="button" class="step-btn" data-act="dec">&minus;</button>'
                        + '<input class="step-input" type="number" min="0" step="1" value="' + qty + '">'
                        + '<button type="button" class="step-btn" data-act="inc">+</button>'
                        + '</div>'
                        + '<button type="button" class="btn moreopts" style="margin-top:10px;">MORE OPTIONS</button>'
                    : '<button type="button" class="btn moreopts" style="margin-top:10px;">ADD WITH MANUAL RATE</button>')
                + '</div>';
        }

        function renderLineState(id) {
            const cardEl = list.querySelector('.pcard[data-id="' + id + '"]');
            if (!cardEl) return;
            const item = byId[id];
            const line = getLine(id);
            const qty = line ? line.qty : 0;

            const input = cardEl.querySelector('.step-input');
            if (input && document.activeElement !== input) input.value = qty;

            cardEl.querySelectorAll('.slabtable tbody tr').forEach(tr => tr.classList.remove('active'));
            const hint = cardEl.querySelector('.linehint');

            if (qty < 1) { if (hint) hint.textContent = ''; return; }

            if (!item.slabs || !item.slabs.length) {
                hint.innerHTML = B.hintHtml(item, qty, line.rate, line.free_qty) + B.stockWarnHtml(item, qty, line.free_qty);
                return;
            }

            const slab = B.resolveSlab(item.slabs, qty);
            const idx = item.slabs.indexOf(slab);
            if (idx >= 0) {
                const tr = cardEl.querySelector('.slabtable tbody tr[data-slab="' + idx + '"]');
                if (tr) tr.classList.add('active');
            }

            hint.innerHTML = B.hintHtml(item, qty, line.rate, line.free_qty) + B.stockWarnHtml(item, qty, line.free_qty);
        }

        function setQty(id, qty) {
            qty = Math.max(0, Math.floor(qty || 0));
            const item = byId[id];
            if (!item || !item.slabs || !item.slabs.length) return;

            let line = getLine(id);

            if (qty === 0) {
                if (line) cart.lines = cart.lines.filter(l => l.product_id !== id);
            } else {
                const slab = B.resolveSlab(item.slabs, qty);
                const rate = Math.round(B.netRate(slab) * 100) / 100;
                const free = B.suggestedFree(slab, qty);
                if (!line) {
                    line = { product_id: id, name: item.name, brand: item.brand, category: item.category, mrp: item.mrp, tax_percent: item.tax_percent || 0, tax_inclusive: item.tax_inclusive !== false, qty: 0, rate: 0, scheme_percent: 0, free_qty: 0, manual_rate: false, manual_free: false };
                    cart.lines.push(line);
                }
                line.qty = qty;
                line.scheme_percent = slab.scheme_percent || 0;
                if (!line.manual_rate) line.rate = rate;
                if (!line.manual_free) line.free_qty = free;
            }

            saveCart();
            renderLineState(id);
            renderCartBar();
        }

        function renderCartBar() {
            const n = cart.lines.length;
            const total = cart.lines.reduce((sum, l) => sum + B.lineAmount(l, l.qty, l.rate), 0);
            document.getElementById('cartcount').textContent = n + (n === 1 ? ' item' : ' items');
            document.getElementById('carttotal').textContent = B.money(total);
        }

        function params(extra) {
            const p = new URLSearchParams();
            if (state.q) p.set('q', state.q);
            if (state.brand_id) p.set('brand_id', state.brand_id);
            if (state.category_id) p.set('category_id', state.category_id);
            p.set('sort', state.sort);
            p.set('with_slabs', '1');
            Object.entries(extra || {}).forEach(([k, v]) => p.set(k, v));
            return p.toString();
        }

        async function fetchList(reset) {
            if (reset) { state.offset = 0; list.innerHTML = ''; }
            loading.hidden = false;
            empty.hidden = true;
            const res = await fetch(dataUrl + '?' + params({ offset: state.offset, limit: 25 }));
            const data = await res.json();
            loading.hidden = true;
            data.items.forEach(i => {
                byId[i.id] = i;
                list.insertAdjacentHTML('beforeend', cardHtml(i));
                renderLineState(i.id);
            });
            state.offset = data.next_offset;
            loadmore.hidden = !data.has_more;
            let endnote = document.getElementById('endnote');
            if (!endnote) { endnote = document.createElement('p'); endnote.id = 'endnote'; endnote.className = 'endnote'; endnote.textContent = '\u2014 End of list \u2014'; loadmore.insertAdjacentElement('afterend', endnote); }
            endnote.hidden = data.has_more || data.total === 0;
            empty.hidden = list.children.length > 0;
            count.textContent = data.total + (data.total === 1 ? ' product' : ' products');
        }

        list.addEventListener('click', e => {
            const more = e.target.closest('.moreopts');
            if (more) {
                openFromCard(more);
                return;
            }
            const btn = e.target.closest('.step-btn');
            if (!btn) return;
            const id = parseInt(btn.closest('.pcard').dataset.id, 10);
            const line = getLine(id);
            const qty = line ? line.qty : 0;
            setQty(id, btn.dataset.act === 'inc' ? qty + 1 : qty - 1);
        });

        function openFromCard(el) {
            const id = parseInt(el.closest('.pcard').dataset.id, 10);
            if (byId[id]) composer.open(byId[id]);
        }

        list.addEventListener('input', e => {
            if (!e.target.classList.contains('step-input')) return;
            const id = parseInt(e.target.closest('.pcard').dataset.id, 10);
            setQty(id, parseInt(e.target.value, 10));
        });

        brandSel.addEventListener('change', () => { state.brand_id = brandSel.value; fetchList(true); });
        catSel.addEventListener('change', () => { state.category_id = catSel.value; fetchList(true); });
        sortSel.addEventListener('change', () => { state.sort = sortSel.value; fetchList(true); });
        loadmore.addEventListener('click', () => fetchList(false));

        document.getElementById('gocart').addEventListener('click', () => {
            window.location.href = "{{ route('billing.cart') }}";
        });

        renderCartBar();
        fetchList(true);
    });
    </script>
@endsection
