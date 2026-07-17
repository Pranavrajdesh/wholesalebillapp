@extends('layouts.app')

@section('title', 'New Bill')

@section('content')
    <h2>New Bill</h2>
    <hr class="rule">

    <p class="muted" style="margin:0 0 10px;">Select the partner you are billing.</p>

    <div class="searchwrap">
        <input type="text" id="psearch" placeholder="Search firm, contact or mobile" autocomplete="off">
        <button type="button" id="pclear" class="xbtn" hidden>&times;</button>
        <div id="psug" class="suggest" hidden></div>
    </div>

    <div style="margin-top:10px;">
        <button type="button" id="heldopen" class="btn btn-outline">HELD BILLS</button>
    </div>

    <div style="margin:14px 0 0;"></div>

    <div id="plist"></div>
    <p id="ploading" class="muted" hidden>Loading&hellip;</p>
    <p id="pempty" class="muted" hidden>No partners found. <a href="{{ route('partners.create') }}">Register a partner</a> first.</p>
    <button id="ploadmore" class="btn btn-outline" hidden>LOAD MORE</button>

    <div id="switchmodal" class="modal" hidden>
        <div class="modal-box">
            <div class="modal-head">
                <span>Cart In Progress</span>
                <button type="button" id="switch-close" class="xbtn">&times;</button>
            </div>
            <div class="cmp-body" id="switch-body"></div>
        </div>
    </div>

    @include('billing._held')

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const B = window.Billing;
        const CART_KEY = 'wba_cart';
        const dataUrl = "{{ route('partners.data') }}";
        const holdUrl = "{{ route('billing.hold.index') }}";
        const catalogueUrl = "{{ route('billing.catalogue') }}";
        const cartUrl = "{{ route('billing.cart') }}";

        const state = { q: '', offset: 0 };
        const list = document.getElementById('plist');
        const loading = document.getElementById('ploading');
        const empty = document.getElementById('pempty');
        const loadmore = document.getElementById('ploadmore');
        const search = document.getElementById('psearch');
        const clearbtn = document.getElementById('pclear');
        const sug = document.getElementById('psug');
        const nav = UI.listKeyNav(search, sug, '.sug-item');
        const switchModal = document.getElementById('switchmodal');
        const switchBody = document.getElementById('switch-body');
        let sugTimer = null;

        function esc(s) {
            const d = document.createElement('div');
            d.textContent = s ?? '';
            return d.innerHTML;
        }

        function readCart() {
            try { return JSON.parse(sessionStorage.getItem(CART_KEY)); } catch (e) { return null; }
        }

        // --- held bills (also serves the nav ?held=1 entry) ---
        const held = B.createHeldBills({
            holdUrl,
            getCart: () => {
                const c = readCart();
                return (c && c.partner) ? c : { partner: null, lines: [] };
            },
            onResume: resumed => {
                sessionStorage.setItem(CART_KEY, JSON.stringify(resumed));
                window.location.href = cartUrl;
            },
        });

        document.getElementById('heldopen').addEventListener('click', () => held.open());

        if (new URLSearchParams(window.location.search).get('held') === '1') {
            held.open();
        }

        // --- partner switch modal ---
        function startBilling(picked) {
            const cart = { partner: picked, lines: [], discount: null };
            sessionStorage.setItem(CART_KEY, JSON.stringify(cart));
            window.location.href = catalogueUrl;
        }

        function resumeExisting() {
            window.location.href = catalogueUrl;
        }

        function pickPartner(picked) {
            const cart = readCart();

            if (cart && cart.partner && cart.partner.id === picked.id) {
                resumeExisting();
                return;
            }

            if (cart && cart.partner && cart.lines && cart.lines.length) {
                switchBody.innerHTML =
                    '<div class="card">'
                    + '<div style="font-weight:600; margin-bottom:4px;">Cart has ' + cart.lines.length + ' item(s) for ' + esc(cart.partner.firm_name) + '.</div>'
                    + '<div class="muted">What should happen to it before billing ' + esc(picked.firm_name) + '?</div>'
                    + '</div>'
                    + '<button type="button" class="btn" id="sw-hold">HOLD &amp; SWITCH</button>'
                    + '<div style="margin-top:8px;"><button type="button" class="btn btn-outline" id="sw-discard">DISCARD &amp; SWITCH</button></div>'
                    + '<div style="margin-top:8px;"><button type="button" class="btn btn-outline" id="sw-cancel">CANCEL</button></div>';
                switchModal.hidden = false;

                document.getElementById('sw-hold').addEventListener('click', async () => {
                    try {
                        await B.api(holdUrl, 'POST', {
                            partner_id: cart.partner.id,
                            payload: { lines: cart.lines, discount: cart.discount || null },
                        });
                        startBilling(picked);
                    } catch (err) {
                        B.notify('Hold failed', 'Could not hold the bill. Please try again.');
                    }
                });

                document.getElementById('sw-discard').addEventListener('click', () => startBilling(picked));
                document.getElementById('sw-cancel').addEventListener('click', () => { switchModal.hidden = true; });
                return;
            }

            startBilling(picked);
        }

        document.getElementById('switch-close').addEventListener('click', () => { switchModal.hidden = true; });
        switchModal.addEventListener('click', e => { if (e.target === switchModal) switchModal.hidden = true; });

        function pickFromEl(el) {
            pickPartner({
                id: parseInt(el.dataset.id, 10),
                firm_name: el.dataset.firm,
                mobile: el.dataset.mobile,
            });
        }

        // --- list + suggestions ---
        function cardHtml(p) {
            return '<div class="card">'
                + '<div style="font-weight:700; font-size:15px;">' + esc(p.firm_name) + '</div>'
                + (p.contact_name ? '<div style="color:#444; font-size:13px;">' + esc(p.contact_name) + '</div>' : '')
                + '<div style="font-size:13.5px; font-weight:600; color:#1a1a1a;">' + esc(p.mobile) + '</div>'
                + '<div style="margin-top:10px;">'
                + '<button type="button" class="btn pickpartner" data-id="' + p.id + '" data-firm="' + esc(p.firm_name) + '" data-mobile="' + esc(p.mobile) + '">START BILLING</button>'
                + '</div></div>';
        }

        async function fetchList(reset) {
            if (reset) { state.offset = 0; list.innerHTML = ''; }
            loading.hidden = false;
            empty.hidden = true;
            const p = new URLSearchParams({ sort: 'recent', offset: state.offset, limit: 25 });
            if (state.q) p.set('q', state.q);
            const res = await fetch(dataUrl + '?' + p);
            const data = await res.json();
            loading.hidden = true;
            data.items.forEach(i => list.insertAdjacentHTML('beforeend', cardHtml(i)));
            state.offset = data.next_offset;
            loadmore.hidden = !data.has_more;
            empty.hidden = list.children.length > 0;
        }

        search.addEventListener('input', () => {
            clearTimeout(sugTimer);
            clearbtn.hidden = search.value === '';
            const term = search.value.trim();
            if (!term) {
                sug.hidden = true;
                nav.reset();
                state.q = '';
                fetchList(true);
                return;
            }
            sugTimer = setTimeout(async () => {
                const res = await fetch(dataUrl + '?' + new URLSearchParams({ q: term, limit: 6 }));
                const data = await res.json();
                if (!data.items.length) { sug.hidden = true; nav.reset(); return; }
                sug.innerHTML = data.items.map(p =>
                    '<div class="sug-item" data-id="' + p.id + '" data-firm="' + esc(p.firm_name) + '" data-mobile="' + esc(p.mobile) + '">'
                    + '<div class="sug-img">' + esc(p.initials) + '</div>'
                    + '<div><div>' + esc(p.firm_name) + '</div>'
                    + '<div class="muted">' + esc(p.mobile) + '</div></div>'
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
                if (active) { pickFromEl(active); return; }
                state.q = search.value.trim();
                sug.hidden = true;
                fetchList(true);
            }
            if (e.key === 'Escape') sug.hidden = true;
        });

        sug.addEventListener('click', e => {
            const item = e.target.closest('.sug-item');
            if (item) pickFromEl(item);
        });

        document.addEventListener('click', e => {
            if (!e.target.closest('.searchwrap')) sug.hidden = true;
            const btn = e.target.closest('.pickpartner');
            if (btn) pickFromEl(btn);
        });

        clearbtn.addEventListener('click', () => {
            search.value = '';
            clearbtn.hidden = true;
            sug.hidden = true;
            nav.reset();
            state.q = '';
            fetchList(true);
            search.focus();
        });

        loadmore.addEventListener('click', () => fetchList(false));

        fetchList(true);
    });
    </script>
@endsection
