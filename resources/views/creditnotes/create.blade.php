@extends('layouts.app')

@section('title', 'Credit Note')

@section('content')
    <div class="card pstrip">
        <div>
            <div class="muted" style="font-size:12px;">CREDIT NOTE FOR</div>
            <div style="font-weight:700;">{{ $partner->firm_name }}</div>
        </div>
        <a href="{{ route('ledger.show', $partner) }}" class="editlink">LEDGER</a>
    </div>

    <h2 style="font-size:20px; margin:6px 0 10px;">New Credit Note</h2>
    <hr class="rule">

    <label for="cn-date">Date</label>
    <input type="date" id="cn-date">

    <label style="margin-top:12px;">Type</label>
    <div style="display:flex; gap:8px;">
        <button type="button" class="btn kindbtn active" data-kind="goods">GOODS RETURN</button>
        <button type="button" class="btn btn-outline kindbtn" data-kind="amount">AMOUNT ONLY</button>
    </div>

    <label for="cn-reason" style="margin-top:12px;">Reason</label>
    <input type="text" id="cn-reason" maxlength="255" placeholder="e.g. damaged goods, rate difference (optional)">

    {{-- ---- goods pane ---- --}}
    <div id="pane-goods" style="margin-top:14px;">
        <div class="searchwrap">
            <input type="text" id="search" placeholder="Scan barcode or search to add returned item" autocomplete="off">
            <button type="button" id="clearsearch" class="xbtn" hidden>&times;</button>
            <div id="suggestions" class="suggest" hidden></div>
        </div>

        <div id="cnlines" style="margin-top:12px;"></div>
        <p id="cnempty" class="muted">No items added yet. Search above to add returned items.</p>
    </div>

    {{-- ---- amount pane ---- --}}
    <div id="pane-amount" style="margin-top:14px;" hidden>
        <label for="cn-amount">Credit amount (Rs)</label>
        <input type="number" id="cn-amount" min="0.01" step="0.01" placeholder="0.00">
    </div>

    <div class="card" id="totalcard" style="margin-top:14px;">
        <div class="sumrow total"><span>TOTAL CREDIT</span><span id="cn-total">&#8377;0.00</span></div>
    </div>

    <button type="button" class="btn" id="cn-save" disabled>SAVE CREDIT NOTE</button>

    <div style="margin-top:8px;">
        <a class="btn btn-outline" href="{{ route('ledger.show', $partner) }}">&larr; BACK TO LEDGER</a>
    </div>

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const B = window.Billing;
        const dataUrl = "{{ route('products.data') }}";
        const storeUrl = "{{ route('creditnotes.store') }}";
        const PARTNER_ID = {{ $partner->id }};

        let kind = 'goods';
        let lines = [];

        const $ = id => document.getElementById(id);

        const t = new Date();
        $('cn-date').value = t.getFullYear() + '-' + String(t.getMonth() + 1).padStart(2, '0') + '-' + String(t.getDate()).padStart(2, '0');

        // ---- kind toggle ----
        document.querySelectorAll('.kindbtn').forEach(b => b.addEventListener('click', () => {
            kind = b.dataset.kind;
            document.querySelectorAll('.kindbtn').forEach(x => {
                x.classList.toggle('active', x === b);
                x.classList.toggle('btn-outline', x !== b);
            });
            $('pane-goods').hidden = kind !== 'goods';
            $('pane-amount').hidden = kind !== 'amount';
            renderTotal();
        }));

        // ---- product search (shared kit) ----
        B.createProductSearch({
            input: $('search'),
            clearBtn: $('clearsearch'),
            sugEl: $('suggestions'),
            dataUrl,
            onPick: item => addLine(item),
            onQuery: () => {},
        });

        function addLine(item) {
            const existing = lines.find(l => l.product_id === item.id);
            if (existing) { existing.qty += 1; renderLines(); return; }

            let rate = '';
            if (item.slabs && item.slabs.length) {
                const slab = B.resolveSlab(item.slabs, 1);
                rate = Math.round(B.netRate(slab) * 100) / 100;
            }
            lines.push({ product_id: item.id, name: item.name, brand: item.brand, mrp: item.mrp, qty: 1, rate: rate });
            renderLines();
        }

        function renderLines() {
            $('cnempty').hidden = lines.length > 0;
            $('cnlines').innerHTML = lines.map((l, i) =>
                '<div class="card" style="margin-bottom:8px;" data-i="' + i + '">'
                + '<div style="display:flex; justify-content:space-between; gap:8px; align-items:flex-start;">'
                + '<div>'
                + '<div style="font-weight:600;">' + B.esc(l.name) + '</div>'
                + '<div class="muted">' + B.esc(l.brand) + ' &middot; MRP ' + B.money(l.mrp) + '</div>'
                + '</div>'
                + '<button type="button" class="xbtn l-remove" style="position:static; width:30px; height:30px; border:1px solid #aaa; border-radius:4px;">&times;</button>'
                + '</div>'
                + '<div class="slabrow" style="margin-top:8px;">'
                + '<div class="fld"><span>Qty returned</span><input type="number" class="l-qty" min="1" step="1" value="' + l.qty + '"></div>'
                + '<div class="fld"><span>Rate &#8377;</span><input type="number" class="l-rate" min="0.01" step="0.01" value="' + l.rate + '"></div>'
                + '</div>'
                + '<div class="l-amt" style="margin-top:8px; font-weight:600; font-size:14px;"></div>'
                + '</div>'
            ).join('');
            lines.forEach((l, i) => updateAmt(i));
            renderTotal();
        }

        function updateAmt(i) {
            const l = lines[i];
            const el = document.querySelector('.card[data-i="' + i + '"] .l-amt');
            if (!el) return;
            el.innerHTML = (l.qty && l.rate > 0)
                ? l.qty + ' &times; ' + B.money(l.rate) + ' = <b>' + B.money(l.qty * l.rate) + '</b>'
                : '<span class="error" style="display:inline;">Set a rate</span>';
        }

        $('cnlines').addEventListener('input', e => {
            const card = e.target.closest('.card');
            if (!card) return;
            const i = parseInt(card.dataset.i, 10);
            if (e.target.classList.contains('l-qty')) {
                lines[i].qty = Math.max(1, Math.floor(parseInt(e.target.value, 10) || 1));
            } else if (e.target.classList.contains('l-rate')) {
                lines[i].rate = parseFloat(e.target.value) || 0;
            } else return;
            updateAmt(i);
            renderTotal();
        });

        $('cnlines').addEventListener('click', e => {
            if (!e.target.closest('.l-remove')) return;
            const i = parseInt(e.target.closest('.card').dataset.i, 10);
            lines.splice(i, 1);
            renderLines();
        });

        $('cn-amount').addEventListener('input', renderTotal);

        function total() {
            if (kind === 'amount') return parseFloat($('cn-amount').value) || 0;
            return lines.reduce((s, l) => s + (l.rate > 0 ? l.qty * l.rate : 0), 0);
        }

        function valid() {
            if (kind === 'amount') return total() > 0;
            return lines.length > 0 && lines.every(l => l.qty >= 1 && l.rate > 0);
        }

        function renderTotal() {
            $('cn-total').textContent = B.money(total());
            $('cn-save').disabled = !valid();
        }

        $('cn-save').addEventListener('click', async () => {
            const btn = $('cn-save');
            btn.disabled = true;
            btn.textContent = 'SAVING\u2026';
            try {
                const body = {
                    partner_id: PARTNER_ID,
                    cn_date: $('cn-date').value,
                    kind: kind,
                    reason: $('cn-reason').value.trim() || null,
                };
                if (kind === 'amount') {
                    body.amount = total();
                } else {
                    body.lines = lines.map(l => ({ product_id: l.product_id, qty: l.qty, rate: l.rate }));
                }
                const res = await B.api(storeUrl, 'POST', body);
                window.location.href = res.url;
            } catch (err) {
                btn.disabled = false;
                btn.textContent = 'SAVE CREDIT NOTE';
                B.notify('Could not save credit note', err && err.message && !err.message.startsWith('Request failed') ? err.message : 'Please check the details and try again.');
            }
        });

        renderTotal();
    });
    </script>
@endsection
