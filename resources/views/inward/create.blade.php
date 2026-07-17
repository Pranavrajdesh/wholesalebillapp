@extends('layouts.app')

@section('title', 'Stock Inward')

@section('content')
    <h2>New Stock Inward</h2>
    <hr class="rule">

    <label for="in-date">Date</label>
    <input type="date" id="in-date">

    <label for="in-supplier" style="margin-top:12px;">Supplier (optional)</label>
    <select id="in-supplier">
        <option value="">&mdash; No supplier &mdash;</option>
        @foreach ($suppliers as $s)
            <option value="{{ $s->id }}">{{ $s->firm_name }}</option>
        @endforeach
    </select>

    <label for="in-note" style="margin-top:12px;">Note</label>
    <input type="text" id="in-note" maxlength="255" placeholder="e.g. weekly Parle delivery (optional)">

    <div style="margin-top:14px;" class="searchwrap">
        <input type="text" id="search" placeholder="Scan barcode or search to add item" autocomplete="off">
        <button type="button" id="clearsearch" class="xbtn" hidden>&times;</button>
        <div id="suggestions" class="suggest" hidden></div>
    </div>

    <div id="inlines" style="margin-top:12px;"></div>
    <p id="inempty" class="muted">No items added yet. Search above to add received stock.</p>

    <div class="card" style="margin-top:14px;">
        <div class="sumrow total"><span>TOTAL UNITS IN</span><span id="in-total">0</span></div>
    </div>

    <div style="margin-top:18px;">
        <button type="button" class="btn" id="in-save" disabled>SAVE INWARD ENTRY</button>
    </div>

    <div style="margin-top:8px;">
        <a class="btn btn-outline" href="{{ route('inward.index') }}">&larr; BACK TO INWARD LIST</a>
    </div>

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const B = window.Billing;
        const dataUrl = "{{ route('products.data') }}";
        const storeUrl = "{{ route('inward.store') }}";

        let lines = [];

        const $ = id => document.getElementById(id);

        const t = new Date();
        $('in-date').value = t.getFullYear() + '-' + String(t.getMonth() + 1).padStart(2, '0') + '-' + String(t.getDate()).padStart(2, '0');

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
            lines.push({ product_id: item.id, name: item.name, brand: item.brand, stock_qty: item.stock_qty, qty: 1, purchase_rate: '' });
            renderLines();
        }

        document.addEventListener('click', e => {
            if (e.target.classList.contains('q-dec') || e.target.classList.contains('q-inc')) {
                const inp = e.target.parentElement.querySelector('.l-qty');
                const cur = Math.max(1, parseInt(inp.value, 10) || 1);
                inp.value = e.target.classList.contains('q-inc') ? cur + 1 : Math.max(1, cur - 1);
                inp.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });

        function renderLines() {
            $('inempty').hidden = lines.length > 0;
            $('inlines').innerHTML = lines.map((l, i) =>
                '<div class="card" style="margin-bottom:8px;" data-i="' + i + '">'
                + '<div style="display:flex; justify-content:space-between; gap:8px; align-items:flex-start;">'
                + '<div>'
                + '<div style="font-weight:600;">' + B.esc(l.name) + '</div>'
                + '<div class="muted">' + B.esc(l.brand) + ' &middot; current stock ' + l.stock_qty + '</div>'
                + '</div>'
                + '<button type="button" class="xbtn l-remove" style="position:static; width:30px; height:30px; border:1px solid #aaa; border-radius:4px;">&times;</button>'
                + '</div>'
                + '<div class="slabrow" style="margin-top:8px;">'
                + '<div class="fld"><span>Qty received</span><div style="display:flex; gap:6px;"><button type="button" class="q-dec" style="width:40px; font-size:17px; border:1px solid #1a1a1a; background:#fff; border-radius:4px; cursor:pointer;">&minus;</button><input type="number" class="l-qty" min="1" step="1" value="' + l.qty + '" style="flex:1; text-align:center;"><button type="button" class="q-inc" style="width:40px; font-size:17px; border:1px solid #1a1a1a; background:#fff; border-radius:4px; cursor:pointer;">+</button></div></div>'
                + '<div class="fld"><span>Purchase rate &#8377; (optional)</span><input type="number" class="l-rate" min="0.01" step="0.01" value="' + l.purchase_rate + '"></div>'
                + '</div>'
                + '<div class="l-after" style="margin-top:8px; font-size:13px; font-weight:600;"></div>'
                + '</div>'
            ).join('');
            lines.forEach((l, i) => updateAfter(i));
            renderTotal();
        }

        function updateAfter(i) {
            const l = lines[i];
            const el = document.querySelector('.card[data-i="' + i + '"] .l-after');
            if (el) el.innerHTML = 'Stock after: <b>' + (l.stock_qty + l.qty) + '</b>';
        }

        $('inlines').addEventListener('input', e => {
            const card = e.target.closest('.card');
            if (!card) return;
            const i = parseInt(card.dataset.i, 10);
            if (e.target.classList.contains('l-qty')) {
                lines[i].qty = Math.max(1, Math.floor(parseInt(e.target.value, 10) || 1));
                updateAfter(i);
                renderTotal();
            } else if (e.target.classList.contains('l-rate')) {
                lines[i].purchase_rate = e.target.value;
            }
        });

        $('inlines').addEventListener('click', e => {
            if (!e.target.closest('.l-remove')) return;
            const i = parseInt(e.target.closest('.card').dataset.i, 10);
            lines.splice(i, 1);
            renderLines();
        });

        function renderTotal() {
            $('in-total').textContent = lines.reduce((s, l) => s + l.qty, 0);
            $('in-save').disabled = lines.length === 0;
        }

        $('in-save').addEventListener('click', async () => {
            const btn = $('in-save');
            btn.disabled = true;
            btn.textContent = 'SAVING\u2026';
            try {
                const res = await B.api(storeUrl, 'POST', {
                    inward_date: $('in-date').value,
                    supplier_id: $('in-supplier').value || null,
                    note: $('in-note').value.trim() || null,
                    lines: lines.map(l => ({
                        product_id: l.product_id,
                        qty: l.qty,
                        purchase_rate: l.purchase_rate !== '' ? parseFloat(l.purchase_rate) : null,
                    })),
                });
                window.location.href = res.url;
            } catch (err) {
                btn.disabled = false;
                btn.textContent = 'SAVE INWARD ENTRY';
                B.notify('Could not save inward entry', err && err.message && !err.message.startsWith('Request failed') ? err.message : 'Please check the details and try again.');
            }
        });

        renderTotal();
    });
    </script>
@endsection
