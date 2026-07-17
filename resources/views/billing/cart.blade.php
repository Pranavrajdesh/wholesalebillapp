@extends('layouts.app')

@section('title', 'Cart')

@section('content')
    <div class="card pstrip">
        <div>
            <div class="muted" style="font-size:12px;">BILLING TO</div>
            <div id="pstripname" style="font-weight:700;"></div>
        </div>
        <a href="{{ route('billing.select') }}" class="editlink">CHANGE</a>
    </div>

    <div class="searchwrap">
        <input type="text" id="search" placeholder="Scan barcode or search to add item" autocomplete="off">
        <button type="button" id="clearsearch" class="xbtn" hidden>&times;</button>
        <div id="suggestions" class="suggest" hidden></div>
    </div>

    <div style="margin-top:10px;">
        <button type="button" class="btn btn-outline heldopen">HELD BILLS</button>
    </div>

    <hr class="rule">
    <h2 style="font-size:20px; margin:6px 0 10px;">Cart</h2>

    <div id="clines"></div>
    <p id="cempty" class="muted" hidden>Cart is empty. Scan a barcode above or browse the catalogue.</p>

    <div class="card" id="discountcard" hidden>
        <div style="font-weight:600; margin-bottom:4px;">Discount</div>
        <div class="slabrow">
            <div class="fld"><span>Type</span>
                <select id="d-type">
                    <option value="amount">&#8377; Amount</option>
                    <option value="percent">% of subtotal</option>
                </select>
            </div>
            <div class="fld"><span>Value</span><input type="number" id="d-value" min="0" step="0.01" placeholder="0"></div>
        </div>
        <label for="d-note" style="margin-top:10px;">Discount note</label>
        <input type="text" id="d-note" placeholder="Optional">
    </div>

    <div class="card" id="summarycard" hidden>
        <div class="sumrow"><span>Subtotal</span><span id="s-sub"></span></div>
        <div class="sumrow" id="s-discrow" hidden><span>Discount</span><span id="s-disc"></span></div>
        <div class="sumrow" id="s-rorow" hidden><span>Round off</span><span id="s-ro"></span></div>
        <div class="sumrow total"><span>TOTAL</span><span id="s-total"></span></div>
    </div>

    <div id="actions" hidden>
        <button type="button" id="proceed" class="btn">PROCEED TO CHECKOUT</button>
        <div style="margin-top:8px;">
            <button type="button" id="hold" class="btn btn-outline">HOLD BILL</button>
        </div>
    </div>

    <div style="margin-top:8px;">
        <a class="btn btn-outline" href="{{ route('billing.catalogue') }}">&larr; CONTINUE ADDING ITEMS</a>
    </div>

    <div style="margin-top:8px;">
        <button type="button" class="btn btn-outline heldopen">HELD BILLS</button>
    </div>

    @include('billing._composer')
    @include('billing._held')

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const B = window.Billing;
        const CART_KEY = 'wba_cart';
        const dataUrl = "{{ route('products.data') }}";
        const holdUrl = "{{ route('billing.hold.index') }}";
        const selectUrl = "{{ route('billing.select') }}";

        let cart = null;
        try { cart = JSON.parse(sessionStorage.getItem(CART_KEY)); } catch (e) { cart = null; }
        if (!cart || !cart.partner) { window.location.href = selectUrl; return; }
        cart.lines = cart.lines || [];
        cart.discount = cart.discount || { type: 'amount', value: 0, note: '' };

        const byId = {};
        const clines = document.getElementById('clines');
        const cempty = document.getElementById('cempty');

        const saveCart = () => sessionStorage.setItem(CART_KEY, JSON.stringify(cart));
        const getLine = id => cart.lines.find(l => l.product_id === id);

        function renderPartner() {
            document.getElementById('pstripname').textContent = cart.partner.firm_name;
        }

        const composer = B.createComposer({
            getCart: () => cart,
            saveCart,
            onChange: () => renderAll(),
        });

        B.createProductSearch({
            input: document.getElementById('search'),
            clearBtn: document.getElementById('clearsearch'),
            sugEl: document.getElementById('suggestions'),
            dataUrl,
            onPick: item => { byId[item.id] = item; composer.open(item); },
            onQuery: () => {},
        });

        const held = B.createHeldBills({
            holdUrl,
            getCart: () => cart,
            onResume: resumed => {
                cart = resumed;
                cart.lines = cart.lines || [];
                cart.discount = cart.discount || { type: 'amount', value: 0, note: '' };
                saveCart();
                renderPartner();
                syncDiscountInputs();
                loadItems().then(renderAll);
            },
        });

        document.querySelectorAll('.heldopen').forEach(b => b.addEventListener('click', () => held.open()));

        async function loadItems() {
            const ids = cart.lines.map(l => l.product_id);
            if (!ids.length) return;
            const res = await fetch(dataUrl + '?' + new URLSearchParams({
                ids: ids.join(','), with_slabs: '1', status: 'all', limit: 50,
            }));
            const data = await res.json();
            data.items.forEach(i => { byId[i.id] = i; });
        }

        function sortedLines() {
            return [...cart.lines].sort((a, b) =>
                a.brand.localeCompare(b.brand)
                || a.category.localeCompare(b.category)
                || a.name.localeCompare(b.name));
        }

        function lineHtml(l) {
            return '<div class="cline card" data-id="' + l.product_id + '">'
                + '<div class="cline-head">'
                + '<div style="font-weight:600;">' + B.esc(l.name) + '</div>'
                + '<button type="button" class="xbtn c-remove" title="Remove">&times;</button>'
                + '</div>'
                + '<div class="muted">MRP ' + B.money(l.mrp)
                + (l.scheme_percent > 0 ? ' &middot; scheme ' + l.scheme_percent + '%' : '') + '</div>'
                + '<div class="slabrow" style="margin-top:8px;">'
                + '<div class="fld"><span>Qty</span><input type="number" class="c-qty" min="1" step="1" value="' + l.qty + '"></div>'
                + '<div class="fld"><span>Rate &#8377;</span><input type="number" class="c-rate" min="0.01" step="0.01" value="' + l.rate + '"></div>'
                + '<div class="fld"><span>Free</span><input type="number" class="c-free" min="0" step="1" value="' + l.free_qty + '"></div>'
                + '</div>'
                + '<div class="cline-amt"></div>'
                + '<div style="margin-top:8px;"><button type="button" class="btn btn-outline c-more" style="padding:7px 16px; font-size:12px;">EDIT</button></div>'
                + '</div>';
        }

        function amtHtml(l) {
            let html = l.qty + ' &times; ' + B.money(l.rate) + B.gstTag(l) + ' = <b>' + B.money(B.lineAmount(l, l.qty, l.rate)) + '</b>';
            if (l.free_qty > 0) html += ' <span class="muted">(+' + l.free_qty + ' free, ships ' + (l.qty + l.free_qty) + ')</span>';
            html += B.stockWarnHtml(byId[l.product_id], l.qty, l.free_qty);
            return html;
        }

        function renderAll() {
            const lines = sortedLines();
            const hasLines = lines.length > 0;
            cempty.hidden = hasLines;
            document.getElementById('discountcard').hidden = !hasLines;
            document.getElementById('summarycard').hidden = !hasLines;
            document.getElementById('actions').hidden = !hasLines;

            let html = '';
            let curBrand = null, curCat = null;
            lines.forEach(l => {
                if (l.brand !== curBrand) {
                    curBrand = l.brand;
                    curCat = null;
                    html += '<div class="bghead">' + B.esc(l.brand) + '</div>';
                }
                if (l.category !== curCat) {
                    curCat = l.category;
                    html += '<div class="cghead muted">' + B.esc(l.category) + '</div>';
                }
                html += lineHtml(l);
            });
            clines.innerHTML = html;

            lines.forEach(l => {
                clines.querySelector('.cline[data-id="' + l.product_id + '"] .cline-amt').innerHTML = amtHtml(l);
            });

            renderTotals();
        }

        function renderTotals() {
            const sub = cart.lines.reduce((s, l) => s + B.lineAmount(l, l.qty, l.rate), 0);
            const d = cart.discount;
            let disc = d.type === 'percent' ? sub * (parseFloat(d.value) || 0) / 100 : (parseFloat(d.value) || 0);
            disc = Math.min(Math.max(disc, 0), sub);
            const net = sub - disc;
            const total = Math.round(net);
            const ro = total - net;

            document.getElementById('s-sub').textContent = B.money(sub);
            document.getElementById('s-discrow').hidden = disc <= 0;
            document.getElementById('s-disc').textContent = '\u2212 ' + B.money(disc);
            document.getElementById('s-rorow').hidden = Math.abs(ro) < 0.005;
            document.getElementById('s-ro').textContent = (ro >= 0 ? '+ ' : '\u2212 ') + B.money(Math.abs(ro));
            document.getElementById('s-total').textContent = B.money(total);
        }

        function recalcLine(l, changed) {
            const item = byId[l.product_id];
            if (changed === 'qty' && item && item.slabs && item.slabs.length) {
                const slab = B.resolveSlab(item.slabs, l.qty);
                if (slab) {
                    l.scheme_percent = slab.scheme_percent || 0;
                    if (!l.manual_rate) l.rate = Math.round(B.netRate(slab) * 100) / 100;
                    if (!l.manual_free) l.free_qty = B.suggestedFree(slab, l.qty);
                }
            }
        }

        clines.addEventListener('input', e => {
            const card = e.target.closest('.cline');
            if (!card) return;
            const l = getLine(parseInt(card.dataset.id, 10));
            if (!l) return;

            if (e.target.classList.contains('c-qty')) {
                l.qty = Math.max(1, Math.floor(parseInt(e.target.value, 10) || 1));
                recalcLine(l, 'qty');
                const rateEl = card.querySelector('.c-rate');
                const freeEl = card.querySelector('.c-free');
                if (document.activeElement !== rateEl) rateEl.value = l.rate;
                if (document.activeElement !== freeEl) freeEl.value = l.free_qty;
            } else if (e.target.classList.contains('c-rate')) {
                const r = parseFloat(e.target.value);
                if (isNaN(r) || r <= 0) return;
                l.rate = Math.round(r * 100) / 100;
                l.manual_rate = true;
            } else if (e.target.classList.contains('c-free')) {
                l.free_qty = Math.max(0, Math.floor(parseInt(e.target.value, 10) || 0));
                l.manual_free = true;
            } else {
                return;
            }

            saveCart();
            card.querySelector('.cline-amt').innerHTML = amtHtml(l);
            renderTotals();
        });

        clines.addEventListener('click', e => {
            const card = e.target.closest('.cline');
            if (!card) return;
            const id = parseInt(card.dataset.id, 10);

            if (e.target.closest('.c-remove')) {
                cart.lines = cart.lines.filter(l => l.product_id !== id);
                saveCart();
                renderAll();
            }

            if (e.target.closest('.c-more')) {
                if (byId[id]) composer.open(byId[id]);
            }
        });

        // --- discount ---
        const dType = document.getElementById('d-type');
        const dValue = document.getElementById('d-value');
        const dNote = document.getElementById('d-note');

        function syncDiscountInputs() {
            dType.value = cart.discount.type;
            dValue.value = cart.discount.value || '';
            dNote.value = cart.discount.note || '';
        }
        syncDiscountInputs();

        [dType, dValue, dNote].forEach(el => el.addEventListener('input', () => {
            cart.discount = { type: dType.value, value: parseFloat(dValue.value) || 0, note: dNote.value.trim() };
            saveCart();
            renderTotals();
        }));

        // --- hold: promptless serialize + empty + back to partner select ---
        document.getElementById('hold').addEventListener('click', async () => {
            if (!cart.lines.length) return;
            try {
                await B.api(holdUrl, 'POST', {
                    partner_id: cart.partner.id,
                    payload: { lines: cart.lines, discount: cart.discount || null },
                });
                sessionStorage.removeItem(CART_KEY);
                window.location.href = selectUrl;
            } catch (err) {
                B.notify('Hold failed', 'Could not hold the bill. Please try again.');
            }
        });

        document.getElementById('proceed').addEventListener('click', () => {
            window.location.href = "{{ route('billing.checkout') }}";
        });

        renderPartner();
        loadItems().then(renderAll);
    });
    </script>
@endsection
