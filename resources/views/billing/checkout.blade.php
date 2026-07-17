@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="card pstrip">
        <div>
            <div class="muted" style="font-size:12px;">BILLING TO</div>
            <div id="pstripname" style="font-weight:700;"></div>
        </div>
        <a href="{{ route('billing.cart') }}" class="editlink">BACK TO CART</a>
    </div>

    <h2 style="font-size:20px; margin:6px 0 10px;">Checkout</h2>
    <hr class="rule">

    <label for="inv-date">Invoice date</label>
    <input type="date" id="inv-date">

    <div id="clines" style="margin-top:14px;"></div>

    <div class="card" id="discview" hidden>
        <div style="font-weight:600;">Discount</div>
        <div class="muted" id="discview-text"></div>
    </div>

    <div class="card" id="summarycard">
        <div class="sumrow"><span>Subtotal</span><span id="s-sub"></span></div>
        <div class="sumrow" id="s-discrow" hidden><span>Discount</span><span id="s-disc"></span></div>
        <div class="sumrow" id="s-rorow" hidden><span>Round off</span><span id="s-ro"></span></div>
        <div class="sumrow total"><span>TOTAL</span><span id="s-total"></span></div>
    </div>

    <button type="button" id="saveinv" class="btn">SAVE INVOICE</button>

    <div style="margin-top:8px;">
        <a class="btn btn-outline" href="{{ route('billing.cart') }}">&larr; BACK TO CART</a>
    </div>

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const B = window.Billing;
        const CART_KEY = 'wba_cart';
        const storeUrl = "{{ route('invoices.store') }}";
        const cartUrl = "{{ route('billing.cart') }}";
        const selectUrl = "{{ route('billing.select') }}";

        let cart = null;
        try { cart = JSON.parse(sessionStorage.getItem(CART_KEY)); } catch (e) { cart = null; }
        if (!cart || !cart.partner) { window.location.href = selectUrl; return; }
        if (!cart.lines || !cart.lines.length) { window.location.href = cartUrl; return; }

        document.getElementById('pstripname').textContent = cart.partner.firm_name;

        const dateEl = document.getElementById('inv-date');
        const today = new Date();
        dateEl.value = today.getFullYear() + '-'
            + String(today.getMonth() + 1).padStart(2, '0') + '-'
            + String(today.getDate()).padStart(2, '0');

        // --- read-only grouped lines ---
        const lines = [...cart.lines].sort((a, b) =>
            a.brand.localeCompare(b.brand)
            || a.category.localeCompare(b.category)
            || a.name.localeCompare(b.name));

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
            html += '<div class="card" style="margin-bottom:8px;">'
                + '<div style="font-weight:600;">' + B.esc(l.name) + '</div>'
                + '<div style="margin-top:4px; font-size:14px;">'
                + l.qty + ' &times; ' + B.money(l.rate) + B.gstTag(l) + ' = <b>' + B.money(B.lineAmount(l, l.qty, l.rate)) + '</b>'
                + (l.free_qty > 0 ? ' <span class="muted">(+' + l.free_qty + ' free, ships ' + (l.qty + l.free_qty) + ')</span>' : '')
                + '</div>'
                + (l.scheme_percent > 0 ? '<div class="muted" style="font-size:12.5px;">Scheme ' + l.scheme_percent + '%</div>' : '')
                + '</div>';
        });
        document.getElementById('clines').innerHTML = html;

        // --- totals (same math as cart) ---
        const sub = cart.lines.reduce((s, l) => s + B.lineAmount(l, l.qty, l.rate), 0);
        const d = cart.discount || { type: 'amount', value: 0, note: '' };
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

        if (disc > 0) {
            document.getElementById('discview').hidden = false;
            document.getElementById('discview-text').textContent =
                (d.type === 'percent' ? d.value + '% of subtotal' : B.money(parseFloat(d.value) || 0).replace('\u20B9', '\u20B9 '))
                + (d.note ? ' \u2014 ' + d.note : '');
        }

        // --- save ---
        const saveBtn = document.getElementById('saveinv');
        saveBtn.addEventListener('click', async () => {
            saveBtn.disabled = true;
            saveBtn.textContent = 'SAVING\u2026';
            try {
                const res = await B.api(storeUrl, 'POST', {
                    partner_id: cart.partner.id,
                    order_id: cart.order_id || null,
                    invoice_date: dateEl.value,
                    lines: cart.lines.map(l => ({
                        product_id: l.product_id,
                        qty: l.qty,
                        free_qty: l.free_qty || 0,
                        scheme_percent: l.scheme_percent || 0,
                        rate: l.rate,
                        manual_rate: !!l.manual_rate,
                        manual_free: !!l.manual_free,
                    })),
                    discount: disc > 0 ? { type: d.type, value: parseFloat(d.value) || 0, note: d.note || null } : null,
                });
                sessionStorage.removeItem(CART_KEY);
                window.location.href = res.url;
            } catch (err) {
                saveBtn.disabled = false;
                saveBtn.textContent = 'SAVE INVOICE';
                B.notify('Could not save invoice', err && err.message && !err.message.startsWith('Request failed') ? err.message : 'Please check the details and try again.');
            }
        });
    });
    </script>
@endsection
