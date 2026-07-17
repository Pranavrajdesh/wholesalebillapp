@extends('layouts.app')

@section('title', 'Retailer Orders')

@section('content')
    <h2>Retailer Orders</h2>
    <hr class="rule">

    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:6px; margin-bottom:12px;" id="statusfilter">
        <button type="button" class="btn btn-outline fltr" data-status="all" style="padding:9px 4px; font-size:11.5px;">ALL</button>
        <button type="button" class="btn fltr active" data-status="pending" style="padding:9px 4px; font-size:11.5px;">PENDING</button>
        <button type="button" class="btn btn-outline fltr" data-status="invoiced" style="padding:9px 4px; font-size:11.5px;">INVOICED</button>
        <button type="button" class="btn btn-outline fltr" data-status="cancelled" style="padding:9px 4px; font-size:11.5px;">CANCELLED</button>
    </div>

    <p id="count" class="count"></p>

    <div id="olist"></div>
    <p id="oloading" class="muted" hidden>Loading&hellip;</p>
    <p id="oempty" class="muted" hidden>No orders here.</p>

    <div id="odetailwrap" class="modal" hidden>
        <div class="modal-box">
            <div class="modal-head">
                <span>Review Order</span>
                <button type="button" id="od-close" class="xbtn">&times;</button>
            </div>
            <div class="cmp-body" id="odetail"></div>
        </div>
    </div>

    <div id="loadmodal" class="modal" hidden>
        <div class="modal-box">
            <div class="modal-head">
                <span>Cart In Progress</span>
                <button type="button" id="lm-close" class="xbtn">&times;</button>
            </div>
            <div class="cmp-body" id="lm-body"></div>
        </div>
    </div>

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const B = window.Billing;
        const CART_KEY = 'wba_cart';
        const dataUrl = "{{ route('orders.data') }}";
        const showUrlBase = "{{ url('/orders') }}";
        const holdUrl = "{{ route('billing.hold.index') }}";
        const cartUrl = "{{ route('billing.cart') }}";

        let status = 'pending';
        let current = null;

        const $ = id => document.getElementById(id);

        function chip(s) {
            const cls = s === 'invoiced' ? 'status' : (s === 'cancelled' ? 'badge-inactive' : '');
            return '<span class="' + cls + '" style="display:inline-block; padding:2px 10px; font-size:11px; font-weight:700; letter-spacing:0.5px; border:1px solid #999; border-radius:999px;">' + s.toUpperCase() + '</span>';
        }

        async function loadList() {
            $('oloading').hidden = false;
            $('oempty').hidden = true;
            const res = await fetch(dataUrl + '?status=' + status);
            const d = await res.json();
            $('oloading').hidden = true;
            $('count').textContent = d.items.length + (d.items.length === 1 ? ' order' : ' orders')
                + (status !== 'pending' ? ' \u00B7 ' + d.pending_count + ' pending' : '');
            $('oempty').hidden = d.items.length > 0;
            $('olist').innerHTML = d.items.map(o =>
                '<div class="card" style="margin-bottom:10px;" data-id="' + o.id + '">'
                + '<div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">'
                + '<div>'
                + '<div style="font-weight:700;">Order #' + o.id + ' &mdash; ' + B.esc(o.firm_name) + '</div>'
                + '<div style="color:#444; font-size:13px;">' + B.esc(o.placed_at) + ' &middot; ' + o.line_count + ' item' + (o.line_count === 1 ? '' : 's') + '</div>'
                + (o.note ? '<div style="margin-top:6px; font-size:13.5px; font-weight:600; color:#1a1a1a; padding:8px 10px; border:1px solid #999; border-left:4px solid #1a1a1a; background:#f7f7f7; border-radius:4px;">NOTE: ' + B.esc(o.note) + '</div>' : '')
                + '</div>'
                + chip(o.status)
                + '</div>'
                + '<div style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;">'
                + '<button type="button" class="btn o-view">REVIEW</button>'
                + (o.invoice_url ? '<a class="btn btn-outline" href="' + o.invoice_url + '">OPEN INVOICE</a>' : '')
                + '</div>'
                + '</div>'
            ).join('');
        }

        document.querySelectorAll('.fltr').forEach(b => b.addEventListener('click', () => {
            status = b.dataset.status;
            document.querySelectorAll('.fltr').forEach(x => {
                x.classList.toggle('active', x === b);
                x.classList.toggle('btn-outline', x !== b);
            });
            $('odetailwrap').hidden = true;
            loadList();
        }));

        $('olist').addEventListener('click', async e => {
            const card = e.target.closest('.card');
            if (!card || !e.target.closest('.o-view')) return;
            await openDetail(parseInt(card.dataset.id, 10));
        });

        async function openDetail(id) {
            const res = await fetch(showUrlBase + '/' + id);
            const o = await res.json();
            current = o;

            let est = 0;
            let unpriced = 0;
            let unavailable = 0;

            let linesHtml = '';
            let curBrand = null;
            o.lines.forEach(l => {
                if (l.brand !== curBrand) {
                    curBrand = l.brand;
                    linesHtml += '<div class="bghead">' + B.esc(l.brand) + '</div>';
                }
                let priceCell = '';
                if (!l.available) {
                    unavailable++;
                    priceCell = '<span class="badge-inactive">UNAVAILABLE</span>';
                } else if (l.slabs && l.slabs.length) {
                    const slab = B.resolveSlab(l.slabs, l.qty);
                    const rate = Math.round(B.netRate(slab) * 100) / 100;
                    const free = B.suggestedFree(slab, l.qty);
                    est += l.qty * rate;
                    priceCell = '@ ' + B.money(rate) + ' = <b>' + B.money(l.qty * rate) + '</b>'
                        + (free > 0 ? ' <span class="muted">(+' + free + ' free)</span>' : '');
                } else {
                    unpriced++;
                    priceCell = '<span class="muted">No standing rate &mdash; set in cart</span>';
                }
                linesHtml += '<div style="border:1px solid #1a1a1a; border-radius:4px; padding:8px 10px; margin:8px 0; font-size:13.5px;">'
                    + '<div style="display:flex; justify-content:space-between; gap:8px;">'
                    + '<span style="font-weight:700;">' + B.esc(l.name) + '</span><b style="white-space:nowrap;">&times; ' + l.qty + '</b>'
                    + '</div>'
                    + '<div style="font-size:12.5px; margin-top:6px; padding-top:6px; border-top:1px dashed #999; color:#1a1a1a; font-weight:600;">' + priceCell + '</div>'
                    + '</div>';
            });

            let html = '<h2 style="font-size:19px; margin:6px 0 8px;">Order #' + o.id + ' &mdash; ' + B.esc(o.partner.firm_name) + '</h2>'
                + '<div style="margin-bottom:8px;">' + chip(o.status) + ' <span class="muted">' + B.esc(o.placed_at) + '</span></div>'
                + (o.note ? '<div class="card"><div style="font-weight:600;">Retailer note</div><div>' + B.esc(o.note) + '</div></div>' : '')
                + linesHtml
                + '<div class="card" style="margin-top:12px;">'
                + '<div class="sumrow"><span>Estimated total (at standing rates)</span><span><b>' + B.money(est) + '</b></span></div>'
                + (unpriced ? '<div class="muted" style="font-size:12.5px;">' + unpriced + ' item(s) need manual rates in the cart.</div>' : '')
                + (unavailable ? '<div class="error" style="margin-top:6px;">' + unavailable + ' item(s) no longer available &mdash; they will be skipped.</div>' : '')
                + '</div>';

            if (o.status === 'pending') {
                html += '<div style="margin-top:12px;"><button type="button" class="btn" id="loadbill">LOAD INTO CART &amp; BILL</button></div>'
                    + '<div style="margin-top:8px;"><button type="button" class="btn btn-outline" id="ocancel">CANCEL ORDER</button></div>';
            } else if (o.invoice_url) {
                html += '<div style="margin-top:12px;"><a class="btn" href="' + o.invoice_url + '">OPEN INVOICE</a></div>';
            }

            $('odetail').innerHTML = html;
            $('odetailwrap').hidden = false;

            const lb = $('loadbill');
            if (lb) lb.addEventListener('click', prepareLoad);
            const oc = $('ocancel');
            if (oc) oc.addEventListener('click', cancelOrder);
        }

        function buildCart() {
            const lines = [];
            current.lines.forEach(l => {
                if (!l.available) return;
                let rate = 0, scheme = 0, free = 0, manual = true;
                if (l.slabs && l.slabs.length) {
                    const slab = B.resolveSlab(l.slabs, l.qty);
                    rate = Math.round(B.netRate(slab) * 100) / 100;
                    scheme = slab.scheme_percent || 0;
                    free = B.suggestedFree(slab, l.qty);
                    manual = false;
                }
                lines.push({
                    product_id: l.product_id,
                    name: l.name, brand: l.brand, category: l.category, mrp: l.mrp,
                    qty: l.qty, rate: rate, scheme_percent: scheme, free_qty: free,
                    manual_rate: manual, manual_free: false,
                });
            });
            return {
                partner: { id: current.partner.id, firm_name: current.partner.firm_name, mobile: current.partner.mobile },
                lines: lines,
                discount: null,
                order_id: current.id,
            };
        }

        function goBill() {
            sessionStorage.setItem(CART_KEY, JSON.stringify(buildCart()));
            window.location.href = cartUrl;
        }

        function prepareLoad() {
            let existing = null;
            try { existing = JSON.parse(sessionStorage.getItem(CART_KEY)); } catch (e) {}

            if (existing && existing.partner && existing.lines && existing.lines.length) {
                $('lm-body').innerHTML =
                    '<div class="card">'
                    + '<div style="font-weight:600; margin-bottom:4px;">Cart has ' + existing.lines.length + ' item(s) for ' + B.esc(existing.partner.firm_name) + '.</div>'
                    + '<div class="muted">What should happen to it before loading this order?</div>'
                    + '</div>'
                    + '<button type="button" class="btn" id="lm-hold">HOLD CURRENT &amp; LOAD ORDER</button>'
                    + '<div style="margin-top:8px;"><button type="button" class="btn btn-outline" id="lm-discard">DISCARD CURRENT &amp; LOAD ORDER</button></div>'
                    + '<div style="margin-top:8px;"><button type="button" class="btn btn-outline" id="lm-cancel">CANCEL</button></div>';
                $('loadmodal').hidden = false;

                $('lm-hold').addEventListener('click', async () => {
                    try {
                        await B.api(holdUrl, 'POST', {
                            partner_id: existing.partner.id,
                            payload: { lines: existing.lines, discount: existing.discount || null },
                        });
                        goBill();
                    } catch (e) {
                        B.notify('Hold failed', 'Could not hold the current cart.');
                    }
                });
                $('lm-discard').addEventListener('click', goBill);
                $('lm-cancel').addEventListener('click', () => { $('loadmodal').hidden = true; });
                return;
            }

            goBill();
        }

        async function cancelOrder() {
            if (!confirm('Cancel order #' + current.id + '?')) return;
            await B.api(showUrlBase + '/' + current.id + '/cancel', 'POST');
            $('odetailwrap').hidden = true;
            loadList();
        }

        $('lm-close').addEventListener('click', () => { $('loadmodal').hidden = true; });
        $('od-close').addEventListener('click', () => { $('odetailwrap').hidden = true; });
        $('odetailwrap').addEventListener('click', e => { if (e.target.id === 'odetailwrap') $('odetailwrap').hidden = true; });

        loadList();
    });
    </script>
@endsection
