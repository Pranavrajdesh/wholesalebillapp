<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1a1a1a">
    <link rel="manifest" href="/manifest-retailer.json">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <title>Retailer Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .portalbar { background: #1a1a1a; color: #fff; padding: 14px max(16px, calc((100% - 650px) / 2 + 16px)); display: flex; justify-content: space-between; align-items: center; }
        .portalbar .store { font-weight: 700; font-size: 16px; }
        .portalbar .who { font-size: 11.5px; color: #bbb; }
        .portalbar button { background: none; border: 1px solid #666; color: #fff; padding: 6px 14px; border-radius: 999px; font-size: 12px; cursor: pointer; }
        .ptabs { background: #fff; border-bottom: 1px solid #ccc; display: flex; padding: 0 max(0px, calc((100% - 650px) / 2)); }
        .ptabs button { flex: 1; padding: 12px; background: none; border: none; font-size: 13px; font-weight: 600; letter-spacing: 0.5px; cursor: pointer; color: #666; border-bottom: 3px solid transparent; }
        .ptabs button.active { color: #1a1a1a; border-bottom-color: #1a1a1a; }
        .pwrap { max-width: 650px; margin: 0 auto; padding: 16px; }
        .ptitle { font-size: 20px; margin: 6px 0 10px; }
        .loginbox { max-width: 400px; margin: 40px auto; background: #fff; border: 1px solid #ccc; border-radius: 6px; padding: 22px; }
        .loginbox h2 { margin: 0 0 4px; }
        .ratenote { font-size: 14px; font-weight: 600; color: #1a1a1a; margin: 10px 0 2px; padding: 10px 12px; border: 1px solid #999; border-left: 4px solid #1a1a1a; background: #f7f7f7; border-radius: 4px; }
        .hiddenv { display: none !important; }
        .statuschip { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; border: 1px solid #999; color: #444; }
        .statuschip.invoiced { background: #e6f4ea; border-color: #1e7e34; color: #1e7e34; }
        .statuschip.cancelled { background: #fdecea; border-color: #b00020; color: #b00020; }
        .vgap { margin-top: 12px; }
        .vgap-sm { margin-top: 8px; }
        .installbar { background: #fff; border-bottom: 1px solid #ccc; }
        .installbar .inner { max-width: 650px; margin: 0 auto; padding: 10px 16px; display: flex; justify-content: space-between; align-items: center; gap: 10px; }
        .installbar .msg { font-size: 12.5px; font-weight: 600; color: #1a1a1a; }
        .installbar .btns { display: flex; gap: 6px; }
        .installbar .go { padding: 8px 14px; background: #1a1a1a; color: #fff; border: none; border-radius: 4px; font-size: 12px; cursor: pointer; }
        .installbar .x { padding: 8px 10px; background: none; border: 1px solid #999; border-radius: 4px; font-size: 12px; cursor: pointer; }
    </style>
</head>
<body>
    {{-- ============ LOGIN ============ --}}
    <div id="loginview">
        <div class="loginbox">
            <h2>Retailer Login</h2>
            <p class="muted" style="margin-top:0;">Order from {{ \App\Models\Setting::getAll()['firm_name'] ?? 'your wholesaler' }}</p>

            <div id="step1">
                <label for="l_mobile">Registered mobile number</label>
                <input type="tel" id="l_mobile" inputmode="numeric" maxlength="10" placeholder="10-digit mobile">
                <div class="error" id="l_err1" style="display:none;"></div>
                <div class="vgap">
                    <button type="button" class="btn" id="l_send">SEND OTP</button>
                    <p style="font-size:13px; color:#444; margin:12px 0 0;">Wholesaler? <a href="{{ route('login') }}" style="color:#1a1a1a; font-weight:600;">Login here &rarr;</a></p>
                </div>
            </div>

            <div id="step2" class="hiddenv">
                <p class="muted" id="l_sentmsg"></p>
                <div class="status" id="l_devotp" style="display:none;"></div>
                <label for="l_code">Enter OTP</label>
                <input type="tel" id="l_code" inputmode="numeric" maxlength="6" placeholder="6-digit code">
                <div class="error" id="l_err2" style="display:none;"></div>
                <div class="vgap">
                    <button type="button" class="btn" id="l_verify">VERIFY &amp; LOGIN</button>
                </div>
                <div class="vgap-sm">
                    <button type="button" class="btn btn-outline" id="l_back">&larr; CHANGE NUMBER</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ APP SHELL ============ --}}
    <div id="appview" class="hiddenv">
        <div class="portalbar">
            <div>
                <div class="store">{{ \App\Models\Setting::getAll()['firm_name'] ?? 'Catalogue' }}</div>
                <div class="who" id="whoami"></div>
            </div>
            <button type="button" id="logout">Logout</button>
        </div>

        <div class="ptabs">
            <button type="button" data-tab="cat" class="active">CATALOGUE</button>
            <button type="button" data-tab="orders">MY ORDERS</button>
        </div>

        <div id="installbar" class="installbar" hidden>
            <div class="inner">
                <span class="msg">Add this shop to your home screen</span>
                <span class="btns">
                    <button type="button" id="pinstall" class="go">INSTALL</button>
                    <button type="button" id="pinstall-x" class="x">&times;</button>
                </span>
            </div>
        </div>

        {{-- ---- catalogue tab ---- --}}
        <div id="tab-cat" class="pwrap">
            <div class="searchwrap">
                <input type="text" id="search" placeholder="Search products" autocomplete="off">
                <button type="button" id="clearsearch" class="xbtn" hidden>&times;</button>
            </div>

            <details class="filterbox">
                <summary>FILTERS</summary>
                <label for="f_brand">Brand</label>
                <select id="f_brand"><option value="">All brands</option></select>
                <label for="f_category">Category</label>
                <select id="f_category"><option value="">All categories</option></select>
            </details>

            <hr class="rule">
            <p id="count" class="count"></p>

            <div id="list"></div>
            <p id="loading" class="muted" hidden>Loading&hellip;</p>
            <p id="empty" class="muted" hidden>No products found.</p>
            <button id="loadmore" class="btn btn-outline" hidden>LOAD MORE</button>
            <div style="height:74px;"></div>
        </div>

        {{-- ---- review screen ---- --}}
        <div id="tab-review" class="pwrap hiddenv">
            <h2 class="ptitle">Your Order</h2>
            <hr class="rule">
            <div id="rlines"></div>
            <p id="rempty" class="muted" hidden>Nothing in your order yet. Add items from the catalogue.</p>

            <div id="rextras">
                <label for="r_note" style="margin-top:14px;">Note to wholesaler</label>
                <input type="text" id="r_note" maxlength="255" placeholder="Optional (e.g. delivery instructions)">

                <div style="margin-top:14px;">
                    <button type="button" class="btn" id="placeorder">PLACE ORDER</button>
                </div>
            </div>
            <div class="vgap-sm">
                <button type="button" class="btn btn-outline" id="backtocat">&larr; CONTINUE ADDING</button>
            </div>
        </div>

        {{-- ---- orders tab ---- --}}
        <div id="tab-orders" class="pwrap hiddenv">
            <h2 class="ptitle">My Orders</h2>
            <hr class="rule">
            <div id="olist"></div>
            <p id="oempty" class="muted" hidden>No orders yet.</p>
        </div>

        {{-- ---- order detail ---- --}}
        <div id="tab-odetail" class="pwrap hiddenv">
            <div id="odetail"></div>
            <div class="vgap-sm">
                <button type="button" class="btn btn-outline" id="backtoorders">&larr; BACK TO ORDERS</button>
            </div>
        </div>

        <div id="cartbar" class="cartbar" style="display:none;">
            <div class="inner">
                <div>
                    <div id="cartcount" style="font-weight:700;">0 items</div>
                    <div class="cartbar-total muted" style="color:#bbb;">Qty-based order</div>
                </div>
                <button type="button" id="gocart" class="cartbar-btn">REVIEW &rarr;</button>
            </div>
        </div>
    </div>

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const B = window.Billing;
        const API = '/api/retailer';
        const TOKEN_KEY = 'wba_rtoken';
        const CART_KEY = 'wba_rcart';

        let token = localStorage.getItem(TOKEN_KEY);
        let cart = { items: {}, note: '' };
        try { cart = JSON.parse(localStorage.getItem(CART_KEY)) || cart; } catch (e) {}
        cart.items = cart.items || {};

        const byId = {};
        const state = { q: '', brand_id: '', category_id: '', offset: 0 };
        let searchTimer = null;

        const $ = id => document.getElementById(id);
        const saveCart = () => localStorage.setItem(CART_KEY, JSON.stringify(cart));

        async function api(path, method = 'GET', body) {
            const res = await fetch(API + path, {
                method,
                headers: {
                    'Accept': 'application/json',
                    ...(token ? { 'Authorization': 'Bearer ' + token } : {}),
                    ...(body ? { 'Content-Type': 'application/json' } : {}),
                },
                body: body ? JSON.stringify(body) : undefined,
            });
            if (res.status === 401) { doLogout(false); throw new Error('unauthorized'); }
            const data = await res.json();
            if (!res.ok) throw data;
            return data;
        }

        function show(view) {
            $('loginview').classList.toggle('hiddenv', view !== 'login');
            $('appview').classList.toggle('hiddenv', view === 'login');
        }

        function showTab(tab) {
            ['cat', 'review', 'orders', 'odetail'].forEach(t => {
                $('tab-' + t).classList.toggle('hiddenv', t !== tab);
            });
            document.querySelectorAll('.ptabs button').forEach(b => {
                b.classList.toggle('active', b.dataset.tab === (tab === 'review' ? 'cat' : (tab === 'odetail' ? 'orders' : tab)));
            });
            $('cartbar').style.display = tab === 'cat' ? '' : 'none';
        }

        document.querySelectorAll('.ptabs button').forEach(b => b.addEventListener('click', () => {
            if (b.dataset.tab === 'orders') loadOrders();
            showTab(b.dataset.tab);
        }));

        // ---------- login ----------
        $('l_send').addEventListener('click', async () => {
            const mobile = $('l_mobile').value.trim();
            const err = $('l_err1');
            err.style.display = 'none';
            if (!/^\d{10}$/.test(mobile)) {
                err.textContent = 'Enter a valid 10-digit mobile number.';
                err.style.display = 'block';
                return;
            }
            try {
                const d = await api('/request-otp', 'POST', { mobile });
                $('l_sentmsg').textContent = 'OTP sent to ' + mobile + '.';
                if (d.dev_otp) {
                    $('l_devotp').textContent = 'Dev OTP: ' + d.dev_otp;
                    $('l_devotp').style.display = 'block';
                }
                $('step1').classList.add('hiddenv');
                $('step2').classList.remove('hiddenv');
                $('l_code').focus();
            } catch (e) {
                err.textContent = e.message || 'Could not send OTP.';
                err.style.display = 'block';
            }
        });

        $('l_verify').addEventListener('click', async () => {
            const err = $('l_err2');
            err.style.display = 'none';
            try {
                const d = await api('/verify-otp', 'POST', {
                    mobile: $('l_mobile').value.trim(),
                    code: $('l_code').value.trim(),
                });
                token = d.token;
                localStorage.setItem(TOKEN_KEY, token);
                enterApp(d.partner);
            } catch (e) {
                err.textContent = e.message || 'Invalid OTP.';
                err.style.display = 'block';
            }
        });

        $('l_back').addEventListener('click', () => {
            $('step2').classList.add('hiddenv');
            $('step1').classList.remove('hiddenv');
        });

        [$('l_mobile'), $('l_code')].forEach(el => el.addEventListener('keydown', e => {
            if (e.key === 'Enter') (el.id === 'l_mobile' ? $('l_send') : $('l_verify')).click();
        }));

        function doLogout(callApi = true) {
            if (callApi && token) { api('/logout', 'POST').catch(() => {}); }
            token = null;
            localStorage.removeItem(TOKEN_KEY);
            $('step2').classList.add('hiddenv');
            $('step1').classList.remove('hiddenv');
            show('login');
        }
        $('logout').addEventListener('click', () => doLogout(true));

        // ---------- catalogue ----------
        async function enterApp(partner) {
            $('whoami').textContent = partner.firm_name;
            show('app');
            showTab('cat');
            const f = await api('/filters');
            const bs = $('f_brand'), cs = $('f_category');
            bs.length = 1; cs.length = 1;
            f.brands.forEach(b => bs.add(new Option(b.name, b.id)));
            f.categories.forEach(c => cs.add(new Option(c.name, c.id)));
            renderCartBar();
            fetchList(true);
        }

        function cardHtml(item) {
            const qty = cart.items[item.id] ? cart.items[item.id].qty : 0;
            return '<div class="card pcard" data-id="' + item.id + '">'
                + '<div class="prow">'
                + (item.image_url
                    ? '<img class="pimg" src="' + item.image_url + '" alt="">'
                    : '<div class="pimg">' + B.esc(item.initials) + '</div>')
                + '<div>'
                + '<div style="font-weight:600;">' + B.esc(item.name) + '</div>'
                + '<div class="muted">' + B.esc(item.brand) + ' &middot; ' + B.esc(item.category) + '</div>'
                + '<div class="mrpline">MRP <b>' + B.money(item.mrp) + '</b></div>'
                + '</div></div>'
                + (item.rates_visible
                    ? '<div class="muted" style="margin-top:10px; font-size:12px; font-weight:600;">RATE BY QUANTITY</div>'
                        + B.slabTableHtml(item)
                    : '<div class="ratenote">Rates on request &mdash; contact us for pricing.</div>')
                + '<div class="stepper">'
                + '<button type="button" class="step-btn" data-act="dec">&minus;</button>'
                + '<input class="step-input" type="number" min="0" step="1" value="' + qty + '">'
                + '<button type="button" class="step-btn" data-act="inc">+</button>'
                + '</div>'
                + '</div>';
        }

        function setQty(id, qty) {
            qty = Math.max(0, Math.floor(qty || 0));
            const item = byId[id];
            if (!item) return;
            if (qty === 0) {
                delete cart.items[id];
            } else {
                cart.items[id] = { product_id: id, name: item.name, brand: item.brand, category: item.category, mrp: item.mrp, qty };
            }
            saveCart();
            const input = document.querySelector('.pcard[data-id="' + id + '"] .step-input');
            if (input && document.activeElement !== input) input.value = qty;
            renderCartBar();
        }

        function renderCartBar() {
            const n = Object.keys(cart.items).length;
            $('cartcount').textContent = n + (n === 1 ? ' item' : ' items');
        }

        function params(extra) {
            const p = new URLSearchParams();
            if (state.q) p.set('q', state.q);
            if (state.brand_id) p.set('brand_id', state.brand_id);
            if (state.category_id) p.set('category_id', state.category_id);
            Object.entries(extra || {}).forEach(([k, v]) => p.set(k, v));
            return p.toString();
        }

        async function fetchList(reset) {
            if (reset) { state.offset = 0; $('list').innerHTML = ''; }
            $('loading').hidden = false;
            $('empty').hidden = true;
            const data = await api('/products?' + params({ offset: state.offset, limit: 25 }));
            $('loading').hidden = true;
            data.items.forEach(i => {
                byId[i.id] = i;
                $('list').insertAdjacentHTML('beforeend', cardHtml(i));
            });
            state.offset = data.next_offset;
            $('loadmore').hidden = !data.has_more;
            let endnote = document.getElementById('endnote');
            if (!endnote) { endnote = document.createElement('p'); endnote.id = 'endnote'; endnote.className = 'endnote'; endnote.textContent = '\u2014 End of list \u2014'; $('loadmore').insertAdjacentElement('afterend', endnote); }
            endnote.hidden = data.has_more || data.total === 0;
            $('empty').hidden = $('list').children.length > 0;
            $('count').textContent = data.total + (data.total === 1 ? ' product' : ' products');
        }

        $('list').addEventListener('click', e => {
            const btn = e.target.closest('.step-btn');
            if (!btn) return;
            const id = parseInt(btn.closest('.pcard').dataset.id, 10);
            const cur = cart.items[id] ? cart.items[id].qty : 0;
            setQty(id, btn.dataset.act === 'inc' ? cur + 1 : cur - 1);
        });

        $('list').addEventListener('input', e => {
            if (!e.target.classList.contains('step-input')) return;
            const id = parseInt(e.target.closest('.pcard').dataset.id, 10);
            setQty(id, parseInt(e.target.value, 10));
        });

        $('search').addEventListener('input', () => {
            clearTimeout(searchTimer);
            $('clearsearch').hidden = $('search').value === '';
            searchTimer = setTimeout(() => {
                state.q = $('search').value.trim();
                fetchList(true);
            }, 300);
        });

        $('clearsearch').addEventListener('click', () => {
            $('search').value = '';
            $('clearsearch').hidden = true;
            state.q = '';
            fetchList(true);
            $('search').focus();
        });

        $('f_brand').addEventListener('change', () => { state.brand_id = $('f_brand').value; fetchList(true); });
        $('f_category').addEventListener('change', () => { state.category_id = $('f_category').value; fetchList(true); });
        $('loadmore').addEventListener('click', () => fetchList(false));

        // ---------- review + place ----------
        $('gocart').addEventListener('click', () => { renderReview(); showTab('review'); });
        $('backtocat').addEventListener('click', () => showTab('cat'));

        function renderReview() {
            const items = Object.values(cart.items).sort((a, b) =>
                a.brand.localeCompare(b.brand) || a.category.localeCompare(b.category) || a.name.localeCompare(b.name));

            $('rempty').hidden = items.length > 0;
            $('rextras').style.display = items.length ? '' : 'none';
            $('r_note').value = cart.note || '';

            let html = '';
            let curBrand = null;
            items.forEach(it => {
                if (it.brand !== curBrand) {
                    curBrand = it.brand;
                    html += '<div class="bghead">' + B.esc(it.brand) + '</div>';
                }
                html += '<div class="card" style="margin-bottom:8px;" data-id="' + it.product_id + '">'
                    + '<div class="dcard-row" style="align-items:flex-start;">'
                    + '<div>'
                    + '<div style="font-weight:600;">' + B.esc(it.name) + '</div>'
                    + '<div class="muted">MRP ' + B.money(it.mrp) + '</div>'
                    + '</div>'
                    + '<button type="button" class="xbtn r-remove" style="position:static; width:30px; height:30px; border:1px solid #aaa; border-radius:4px;">&times;</button>'
                    + '</div>'
                    + '<div class="stepper" style="margin-top:8px;">'
                    + '<button type="button" class="step-btn" data-act="dec">&minus;</button>'
                    + '<input class="step-input" type="number" min="1" step="1" value="' + it.qty + '">'
                    + '<button type="button" class="step-btn" data-act="inc">+</button>'
                    + '</div>'
                    + '</div>';
            });
            $('rlines').innerHTML = html;
        }

        $('rlines').addEventListener('click', e => {
            const card = e.target.closest('.card');
            if (!card) return;
            const id = parseInt(card.dataset.id, 10);

            if (e.target.closest('.r-remove')) {
                delete cart.items[id];
                saveCart();
                renderCartBar();
                renderReview();
                return;
            }

            const btn = e.target.closest('.step-btn');
            if (btn && cart.items[id]) {
                const next = cart.items[id].qty + (btn.dataset.act === 'inc' ? 1 : -1);
                if (next < 1) return;
                cart.items[id].qty = next;
                saveCart();
                card.querySelector('.step-input').value = next;
            }
        });

        $('rlines').addEventListener('input', e => {
            if (!e.target.classList.contains('step-input')) return;
            const id = parseInt(e.target.closest('.card').dataset.id, 10);
            const v = Math.max(1, Math.floor(parseInt(e.target.value, 10) || 1));
            if (cart.items[id]) { cart.items[id].qty = v; saveCart(); }
        });

        $('r_note').addEventListener('input', () => { cart.note = $('r_note').value; saveCart(); });

        $('placeorder').addEventListener('click', async () => {
            const items = Object.values(cart.items);
            if (!items.length) return;
            const btn = $('placeorder');
            btn.disabled = true;
            btn.textContent = 'PLACING\u2026';
            try {
                await api('/orders', 'POST', {
                    lines: items.map(it => ({ product_id: it.product_id, qty: it.qty })),
                    note: (cart.note || '').trim() || null,
                });
                cart = { items: {}, note: '' };
                saveCart();
                renderCartBar();
                await loadOrders();
                showTab('orders');
            } catch (e) {
                B.notify('Order failed', 'Could not place the order. Please try again.');
            }
            btn.disabled = false;
            btn.textContent = 'PLACE ORDER';
        });

        // ---------- orders ----------
        function chip(status) {
            return '<span class="statuschip ' + status + '">' + status.toUpperCase() + '</span>';
        }

        async function loadOrders() {
            const d = await api('/orders');
            $('oempty').hidden = d.items.length > 0;
            $('olist').innerHTML = d.items.map(o =>
                '<div class="card" style="margin-bottom:10px;" data-id="' + o.id + '">'
                + '<div class="dcard-row" style="align-items:flex-start;">'
                + '<div>'
                + '<div style="font-weight:700;">Order #' + o.id + '</div>'
                + '<div class="muted">' + B.esc(o.placed_at) + ' &middot; ' + o.line_count + ' item' + (o.line_count === 1 ? '' : 's') + '</div>'
                + '</div>'
                + chip(o.status)
                + '</div>'
                + '<div style="margin-top:10px; display:flex; gap:8px;">'
                + '<button type="button" class="btn o-open">VIEW</button>'
                + (o.invoice_url ? '<a class="btn btn-outline" href="' + o.invoice_url + '" target="_blank">INVOICE</a>' : '')
                + '</div>'
                + '</div>'
            ).join('');
        }

        $('olist').addEventListener('click', async e => {
            const card = e.target.closest('.card');
            if (!card || !e.target.closest('.o-open')) return;
            await openOrder(parseInt(card.dataset.id, 10));
        });

        function uiConfirm(message, yesLabel) {
            return new Promise(resolve => {
                const ov = document.createElement('div');
                ov.style.cssText = 'position:fixed; inset:0; background:rgba(0,0,0,0.55); display:flex; align-items:center; justify-content:center; padding:16px; z-index:60;';
                ov.innerHTML = '<div style="background:#fff; border-radius:6px; width:100%; max-width:340px; padding:16px;">'
                    + '<div style="font-weight:700; font-size:15px; color:#1a1a1a;">' + message + '</div>'
                    + '<div style="display:flex; gap:8px; margin-top:14px;">'
                    + '<button type="button" data-a="no" style="flex:1; padding:11px; background:#fff; color:#1a1a1a; border:1px solid #1a1a1a; border-radius:4px; font-size:13px; cursor:pointer;">KEEP</button>'
                    + '<button type="button" data-a="yes" style="flex:1; padding:11px; background:#b00020; color:#fff; border:none; border-radius:4px; font-size:13px; cursor:pointer;">' + (yesLabel || 'YES') + '</button>'
                    + '</div></div>';
                ov.addEventListener('click', e => {
                    const a = e.target.dataset && e.target.dataset.a;
                    if (a || e.target === ov) { ov.remove(); resolve(a === 'yes'); }
                });
                document.body.appendChild(ov);
            });
        }

        async function openOrder(id) {
            const d = await api('/orders/' + id);
            const o = d.order;
            let html = '<h2 class="ptitle">Order #' + o.id + '</h2><hr class="rule">'
                + '<div style="margin:8px 0;">' + chip(o.status) + ' <span class="muted">' + B.esc(o.placed_at) + '</span></div>';

            let curBrand = null;
            o.lines.forEach(l => {
                if (l.brand !== curBrand) {
                    curBrand = l.brand;
                    html += '<div class="bghead">' + B.esc(l.brand) + '</div>';
                }
                html += '<div class="dcard" style="margin:8px 0; font-size:13.5px;">'
                    + '<div class="dcard-row">'
                    + '<span style="font-weight:700;">' + B.esc(l.name) + '</span>'
                    + '<b class="moneyline">&times; ' + l.qty + '</b>'
                    + '</div>'
                    + '<div class="dcard-part" style="font-size:12.5px; font-weight:600;">MRP ' + B.money(l.mrp) + '</div>'
                    + '</div>';
            });

            if (o.note) {
                html += '<div class="card" style="margin-top:12px;"><div style="font-weight:600;">Note</div><div>' + B.esc(o.note) + '</div></div>';
            }

            if (o.invoice_url) {
                html += '<div class="vgap"><a class="btn" href="' + o.invoice_url + '" target="_blank">VIEW INVOICE</a></div>';
            }

            if (o.status === 'pending') {
                html += '<div class="vgap"><button type="button" class="btn btn-outline" id="o-cancel" data-id="' + o.id + '">CANCEL ORDER</button></div>';
            }

            $('odetail').innerHTML = html;
            showTab('odetail');

            const cbtn = $('o-cancel');
            if (cbtn) {
                cbtn.addEventListener('click', async () => {
                    if (!(await uiConfirm('Cancel this order?', 'CANCEL ORDER'))) return;
                    await api('/orders/' + cbtn.dataset.id + '/cancel', 'POST');
                    await loadOrders();
                    showTab('orders');
                });
            }
        }

        $('backtoorders').addEventListener('click', () => showTab('orders'));

        // ---------- boot ----------
        if (token) {
            api('/me').then(d => enterApp(d.partner)).catch(() => {});
        }
    });
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
        }
        let deferredInstall = null;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredInstall = e;
            document.getElementById('installbar').hidden = false;
        });
        document.addEventListener('click', (e) => {
            if (e.target.closest('#pinstall-x')) { document.getElementById('installbar').hidden = true; return; }
            if (!e.target.closest('#pinstall') || !deferredInstall) return;
            deferredInstall.prompt();
            deferredInstall.userChoice.then(() => {
                deferredInstall = null;
                document.getElementById('installbar').hidden = true;
            });
        });
        window.addEventListener('appinstalled', () => { document.getElementById('installbar').hidden = true; });
    </script>
</body>
</html>
