<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Supplier Ledger &mdash; {{ $supplier->firm_name }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, sans-serif; color: #111; background: #ececec; }
        .sheet { max-width: 800px; margin: 16px auto; background: #fff; padding: 24px 28px; border: 1px solid #ccc; }
        .topbar { max-width: 800px; margin: 12px auto 0; display: flex; gap: 8px; padding: 0 8px; flex-wrap: wrap; }
        .tbtn { flex: 1; min-width: 142px; padding: 11px; text-align: center; background: #1a1a1a; color: #fff; text-decoration: none; border: none; font-size: 13px; cursor: pointer; border-radius: 4px; }
        .tbtn.outline { background: #fff; color: #1a1a1a; border: 1px solid #1a1a1a; }
        .head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #111; padding-bottom: 12px; }
        .firm { font-size: 20px; font-weight: 700; }
        .firmmeta { font-size: 12px; color: #555; line-height: 1.5; }
        .doc { text-align: right; }
        .doc .t { font-size: 12px; letter-spacing: 1px; color: #555; }
        .party { margin: 12px 0; font-size: 13px; line-height: 1.5; }
        .party .label { font-size: 11px; font-weight: 700; color: #666; letter-spacing: 0.5px; }
        .party .pname { font-weight: 700; font-size: 15px; }
        .filters { display: flex; gap: 8px; flex-wrap: wrap; align-items: flex-end; }
        .filters .f { display: flex; flex-direction: column; font-size: 11px; color: #555; gap: 3px; }
        .filters input { padding: 8px; border: 1px solid #aaa; border-radius: 4px; font-size: 13px; }
        .filterbox { border: 1.5px solid #1a1a1a; border-radius: 4px; margin: 10px 0 14px; }
        .filterbox .frow { margin: 0; padding: 10px 12px; border-bottom: 1px solid #999; }
        .filterbox .frow.last { border-bottom: none; }
        .fybtn { padding: 9px 12px; background: #fff; border: 1px solid #1a1a1a; border-radius: 4px; font-size: 12px; cursor: pointer; }
        .fybtn.active { background: #1a1a1a; color: #fff; }
        .tfbtn { padding: 9px 12px; background: #fff; border: 1px solid #1a1a1a; border-radius: 4px; font-size: 12px; cursor: pointer; }
        .tfbtn.active { background: #1a1a1a; color: #fff; }
        table.led { width: 100%; border-collapse: collapse; font-size: 12.5px; border: 1.5px solid #1a1a1a; }
        table.led th { text-align: left; border: 1px solid #555; border-top: 1.5px solid #1a1a1a; border-bottom: 1.5px solid #1a1a1a; padding: 6px; font-size: 11.5px; background: #f2f2f2; }
        table.led td { padding: 5px 6px; border: 1px solid #999; vertical-align: top; }
        table.led .num { text-align: right; white-space: nowrap; }
        table.led tr.open td, table.led tr.tot td { font-weight: 700; background: #f7f7f7; }
        .detail { font-size: 11px; color: #333; }
        .balline { margin-top: 14px; border: 1px solid #bbb; border-left: 4px solid #111; padding: 10px 12px; font-size: 14px; font-weight: 600; }
        .balline b { font-size: 18px; white-space: nowrap; }
        .balline.due { color: #b00020; border-color: #b00020; background: #fdf3f3; }
        .balline.adv { color: #9a6700; border-color: #9a6700; background: #fdf8ec; }
        .balline.ok { color: #1e7e34; border-color: #1e7e34; background: #e6f4ea; }
        .cards { display: none; }
        .lcard { position: relative; background: #fff; border: 1px solid #1a1a1a; border-radius: 4px; padding: 12px 10px 8px; margin-top: 22px; margin-bottom: 0; font-size: 13px; }
        .lcard + .lcard::before { content: ''; position: absolute; top: -16px; left: -4px; right: -4px; border-top: 1px dashed #999; }
        .ltag { position: absolute; top: -9px; left: 10px; padding: 1px 8px; border-radius: 3px; font-size: 9.5px; font-weight: 700; letter-spacing: 0.5px; background: #fff; }
        .ltag.dr { color: #b00020; border: 1px solid #b00020; }
        .ltag.cr { color: #1e7e34; border: 1px solid #1e7e34; }
        .lcard .r1 { display: flex; justify-content: space-between; font-weight: 600; }
        .lcard .r2 { display: flex; justify-content: space-between; margin-top: 6px; padding-top: 6px; border-top: 1px dashed #999; }
        .muted { color: #666; }
        .smodal { position: fixed; inset: 0; background: rgba(0,0,0,0.55); display: flex; align-items: center; justify-content: center; padding: 16px; z-index: 50; }
        .smodal[hidden] { display: none; }
        .smodal .sbox { background: #fff; border-radius: 6px; width: 100%; max-width: 380px; padding: 16px; max-height: 90vh; overflow: auto; }
        .smodal .stitle { font-weight: 700; font-size: 16px; display: flex; justify-content: space-between; align-items: center; }
        .smodal .sx { background: none; border: none; font-size: 24px; cursor: pointer; line-height: 1; }
        .smodal label { display: block; font-size: 13px; margin: 12px 0 4px; font-weight: 600; }
        .smodal input, .smodal select { width: 100%; padding: 10px; font-size: 15px; border: 1px solid #aaa; border-radius: 4px; }
        .smodal .err { color: #b00020; font-size: 12.5px; margin-top: 6px; display: none; }
        .smodal .sgo { width: 100%; margin-top: 14px; padding: 12px; background: #1a1a1a; color: #fff; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; }
        @media (max-width: 640px) {
            .sheet { margin: 8px; padding: 14px 12px; background: transparent; border: none; }
            .filterbox { background: #fff; }
            .head { flex-direction: column; gap: 8px; }
            .doc { text-align: left; }
            .tblwrap { display: none; }
            .cards { display: block; }
        }
        @media print {
            body { background: #fff; }
            .sheet { max-width: none; margin: 0; border: none; padding: 0; }
            .topbar, .filters, .filterbox { display: none; }
            .tblwrap { display: block !important; }
            .cards { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <a class="tbtn outline" href="{{ route('products.index') }}">HOME</a>
        <a class="tbtn outline" href="{{ route('suppliers.index') }}">ALL SUPPLIERS</a>
        <button type="button" class="tbtn" onclick="openActions()">&#9776; MORE ACTIONS</button>
    </div>

    <div class="sheet">
        <div class="head">
            <div>
                <div class="firm">{{ $s['firm_name'] ?? 'Ledger' }}</div>
                <div class="firmmeta">
                    @if (!empty($s['firm_gst'])) GSTIN: {{ $s['firm_gst'] }}<br>@endif
                    Mobile: {{ $s['firm_mobile'] ?? '' }}
                </div>
            </div>
            <div class="doc">
                <div class="t">SUPPLIER ACCOUNT</div>
                <div style="font-size:13px;" id="periodlabel">All transactions</div>
            </div>
        </div>

        <div class="party">
            <div class="label">ACCOUNT OF</div>
            <div class="pname">{{ $supplier->firm_name }}</div>
            <div>Mobile: {{ $supplier->mobile }}</div>
            @if ($supplier->gst_number)<div>GSTIN: {{ $supplier->gst_number }}</div>@endif
        </div>

        <div class="filterbox">
            <div class="filters frow">
                <div class="f"><span>From</span><input type="date" id="f_from"></div>
                <div class="f"><span>To</span><input type="date" id="f_to"></div>
            </div>
            <div class="filters frow">
                <button type="button" class="fybtn" data-fy="this">THIS FY</button>
                <button type="button" class="fybtn" data-fy="last">LAST FY</button>
                <button type="button" class="fybtn active" data-fy="all">ALL</button>
            </div>
            <div class="filters frow last">
                <button type="button" class="tfbtn active" data-tf="all">ALL TYPES</button>
                <button type="button" class="tfbtn" data-tf="bill">BILLS</button>
                <button type="button" class="tfbtn" data-tf="payment">PAYMENTS</button>
            </div>
        </div>

        <div class="tblwrap">
            <table class="led">
                <thead>
                    <tr>
                        <th style="width:78px;">Date</th>
                        <th>Particulars</th>
                        <th class="num" style="width:90px;">Debit</th>
                        <th class="num" style="width:90px;">Credit</th>
                        <th class="num" style="width:100px;">Balance</th>
                    </tr>
                </thead>
                <tbody id="ltbody"></tbody>
            </table>
        </div>

        <div class="cards" id="lcards"></div>

        <p id="lempty" hidden style="font-size:13.5px; font-weight:600; color:#1a1a1a;">No transactions in this period.</p>

        <div class="balline" id="balline" hidden></div>
    </div>

    <div id="actionsmodal" class="smodal" hidden>
        <div class="sbox">
            <div class="stitle">
                <span>Supplier Ledger Actions</span>
                <button type="button" class="sx" onclick="closeActions()">&times;</button>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px; margin-top:14px;">
                <button type="button" class="tbtn" onclick="closeActions(); openBill()">RECORD BILL</button>
                <button type="button" class="tbtn" onclick="closeActions(); openPay()">RECORD PAYMENT</button>
                <button type="button" class="tbtn" onclick="closeActions(); window.print()">PRINT</button>
                <a class="tbtn" id="pdfbtn" href="#">DOWNLOAD PDF</a>
            </div>
        </div>
    </div>

    <div id="billmodal" class="smodal" hidden>
        <div class="sbox">
            <div class="stitle">
                <span>Record Supplier Bill</span>
                <button type="button" class="sx" onclick="closeBill()">&times;</button>
            </div>
            <label for="b_no">Supplier bill no.</label>
            <input type="text" id="b_no" maxlength="100" placeholder="Optional">
            <label for="b_date">Bill date</label>
            <input type="date" id="b_date">
            <label for="b_amount">Amount (Rs)</label>
            <input type="number" id="b_amount" min="0.01" step="0.01" placeholder="0.00">
            <label for="b_note">Note</label>
            <input type="text" id="b_note" maxlength="255" placeholder="Optional">
            <div class="err" id="b_err">Enter a valid amount.</div>
            <button type="button" class="sgo" onclick="saveBill()">SAVE BILL</button>
        </div>
    </div>

    <div id="paymodal" class="smodal" hidden>
        <div class="sbox">
            <div class="stitle">
                <span>Record Payment to Supplier</span>
                <button type="button" class="sx" onclick="closePay()">&times;</button>
            </div>
            <label for="p_date">Payment date</label>
            <input type="date" id="p_date">
            <label for="p_amount">Amount (Rs)</label>
            <input type="number" id="p_amount" min="0.01" step="0.01" placeholder="0.00">
            <label for="p_method">Method</label>
            <select id="p_method">
                <option value="cash">Cash</option>
                <option value="upi">UPI</option>
                <option value="bank">Bank transfer</option>
                <option value="cheque">Cheque</option>
                <option value="other">Other</option>
            </select>
            <label for="p_ref">Reference (UTR / cheque no.)</label>
            <input type="text" id="p_ref" maxlength="100" placeholder="Optional">
            <label for="p_note">Note</label>
            <input type="text" id="p_note" maxlength="255" placeholder="Optional">
            <div class="err" id="p_err">Enter a valid amount.</div>
            <button type="button" class="sgo" onclick="savePayment()">SAVE PAYMENT</button>
        </div>
    </div>

    <script>
        const DATA_URL = @json($dataUrl);
        const SUPPLIER_ID = @json($supplier->id);
        const BILL_URL = "{{ route('supplier.bills.store') }}";
        const PAY_URL = "{{ route('supplier.payments.store') }}";

        let LAST_D = null;
        const state = { from: '', to: '', tf: 'all' };

        const money = n => 'Rs ' + (Math.round(n * 100) / 100).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const esc = s => { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; };
        const fmtDate = iso => { const [y, m, d] = iso.split('-'); return d + '/' + m + '/' + y.slice(2); };
        const today = () => { const t = new Date(); return t.getFullYear() + '-' + String(t.getMonth() + 1).padStart(2, '0') + '-' + String(t.getDate()).padStart(2, '0'); };
        const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

        async function load() {
            let url = DATA_URL;
            const p = new URLSearchParams();
            if (state.from) p.set('from', state.from);
            if (state.to) p.set('to', state.to);
            const qs = p.toString();
            if (qs) url += (url.includes('?') ? '&' : '?') + qs;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const d = await res.json();
            render(d);
        }

        function render(full) {
            LAST_D = full;
            const typeMatch = e => state.tf === 'all'
                || (state.tf === 'bill' && e.credit > 0)
                || (state.tf === 'payment' && e.debit > 0);
            const d = state.tf === 'all' ? full : Object.assign({}, full, {
                entries: full.entries.filter(typeMatch),
            });
            if (state.tf !== 'all') {
                d.total_debit = d.entries.reduce((s, e) => s + e.debit, 0);
                d.total_credit = d.entries.reduce((s, e) => s + e.credit, 0);
            }

            document.getElementById('pdfbtn').href = full.pdf_url;

            document.getElementById('periodlabel').textContent =
                (state.from || state.to)
                    ? (state.from ? fmtDate(state.from) : 'Start') + ' \u2013 ' + (state.to ? fmtDate(state.to) : 'Today')
                    : 'All transactions';

            const tbody = document.getElementById('ltbody');
            const cards = document.getElementById('lcards');
            let rows = '';
            let cardhtml = '';

            if (state.from) {
                rows += '<tr class="open"><td></td><td>Opening balance</td><td class="num"></td><td class="num"></td><td class="num">' + money(full.opening) + '</td></tr>';
                cardhtml += '<div class="lcard"><div class="r1"><span>Opening balance</span><span>' + money(full.opening) + '</span></div></div>';
            }

            d.entries.forEach(e => {
                rows += '<tr>'
                    + '<td>' + fmtDate(e.date) + '</td>'
                    + '<td>' + esc(e.label) + (e.detail ? '<div class="detail">' + esc(e.detail) + '</div>' : '') + '</td>'
                    + '<td class="num">' + (e.debit > 0 ? money(e.debit) : '') + '</td>'
                    + '<td class="num">' + (e.credit > 0 ? money(e.credit) : '') + '</td>'
                    + '<td class="num">' + money(e.balance) + '</td>'
                    + '</tr>';

                cardhtml += '<div class="lcard">'
                    + (e.credit > 0 ? '<span class="ltag dr">BILL</span>' : '<span class="ltag cr">PAYMENT</span>')
                    + '<div class="r1"><span>' + esc(e.label) + '</span><span>' + (e.credit > 0 ? money(e.credit) : '- ' + money(e.debit)) + '</span></div>'
                    + '<div class="r2" style="color:#444;"><span>' + fmtDate(e.date) + (e.detail ? ' \u00B7 <b style="color:#1a1a1a;">' + esc(e.detail) + '</b>' : '') + '</span><span style="color:#1a1a1a; font-weight:700;">Bal ' + money(e.balance) + '</span></div>'
                    + '</div>';
            });

            if (d.entries.length) {
                rows += '<tr class="tot"><td></td><td>Total</td><td class="num">' + money(d.total_debit) + '</td><td class="num">' + money(d.total_credit) + '</td><td class="num">' + money(full.closing) + '</td></tr>';
            }

            tbody.innerHTML = rows;
            cards.innerHTML = cardhtml;
            document.getElementById('lempty').hidden = d.entries.length > 0;

            const bal = document.getElementById('balline');
            bal.hidden = false;
            if (full.closing > 0) {
                bal.className = 'balline due';
                bal.innerHTML = 'Payable to ' + esc(full.supplier.firm_name) + ': <b>' + money(full.closing) + '</b>';
            } else if (full.closing < 0) {
                bal.className = 'balline adv';
                bal.innerHTML = 'Advance paid to ' + esc(full.supplier.firm_name) + ': <b>' + money(Math.abs(full.closing)) + '</b>';
            } else {
                bal.className = 'balline ok';
                bal.innerHTML = '\u2713 Account settled: <b>' + money(0) + '</b>';
            }
        }

        // --- filters + FY presets ---
        const fromEl = document.getElementById('f_from');
        const toEl = document.getElementById('f_to');

        function setActive(btn) {
            document.querySelectorAll('.fybtn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');
        }

        fromEl.addEventListener('change', () => { state.from = fromEl.value; setActive(null); load(); });
        toEl.addEventListener('change', () => { state.to = toEl.value; setActive(null); load(); });

        document.querySelectorAll('.fybtn').forEach(b => b.addEventListener('click', () => {
            const now = new Date();
            const fyStartYear = now.getMonth() >= 3 ? now.getFullYear() : now.getFullYear() - 1;
            if (b.dataset.fy === 'this') {
                state.from = fyStartYear + '-04-01';
                state.to = '';
            } else if (b.dataset.fy === 'last') {
                state.from = (fyStartYear - 1) + '-04-01';
                state.to = fyStartYear + '-03-31';
            } else {
                state.from = '';
                state.to = '';
            }
            fromEl.value = state.from;
            toEl.value = state.to;
            setActive(b);
            load();
        }));

        // --- type filter chips (independent radio group; re-render from cache) ---
        document.querySelectorAll('.tfbtn').forEach(b => b.addEventListener('click', () => {
            state.tf = b.dataset.tf;
            document.querySelectorAll('.tfbtn').forEach(x => x.classList.toggle('active', x === b));
            if (LAST_D) render(LAST_D);
        }));

        // --- actions modal ---
        function openActions() { document.getElementById('actionsmodal').hidden = false; }
        function closeActions() { document.getElementById('actionsmodal').hidden = true; }
        document.getElementById('actionsmodal').addEventListener('click', e => { if (e.target.id === 'actionsmodal') closeActions(); });

        // --- record bill ---
        function openBill() {
            document.getElementById('b_date').value = today();
            document.getElementById('billmodal').hidden = false;
            document.getElementById('b_no').focus();
        }
        function closeBill() { document.getElementById('billmodal').hidden = true; }

        async function saveBill() {
            const amount = parseFloat(document.getElementById('b_amount').value);
            const err = document.getElementById('b_err');
            if (isNaN(amount) || amount <= 0) { err.style.display = 'block'; return; }
            err.style.display = 'none';
            try {
                const res = await fetch(BILL_URL, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                    body: JSON.stringify({
                        supplier_id: SUPPLIER_ID,
                        bill_no: document.getElementById('b_no').value.trim() || null,
                        bill_date: document.getElementById('b_date').value,
                        amount: amount,
                        note: document.getElementById('b_note').value.trim() || null,
                    }),
                });
                if (!res.ok) throw new Error('save failed');
                document.getElementById('b_no').value = '';
                document.getElementById('b_amount').value = '';
                document.getElementById('b_note').value = '';
                closeBill();
                load();
            } catch (e) {
                err.textContent = 'Could not save. Please try again.';
                err.style.display = 'block';
            }
        }

        // --- record payment ---
        function openPay() {
            document.getElementById('p_date').value = today();
            document.getElementById('paymodal').hidden = false;
            document.getElementById('p_amount').focus();
        }
        function closePay() { document.getElementById('paymodal').hidden = true; }

        async function savePayment() {
            const amount = parseFloat(document.getElementById('p_amount').value);
            const err = document.getElementById('p_err');
            if (isNaN(amount) || amount <= 0) { err.style.display = 'block'; return; }
            err.style.display = 'none';
            try {
                const res = await fetch(PAY_URL, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                    body: JSON.stringify({
                        supplier_id: SUPPLIER_ID,
                        payment_date: document.getElementById('p_date').value,
                        amount: amount,
                        method: document.getElementById('p_method').value,
                        reference: document.getElementById('p_ref').value.trim() || null,
                        note: document.getElementById('p_note').value.trim() || null,
                    }),
                });
                if (!res.ok) throw new Error('save failed');
                document.getElementById('p_amount').value = '';
                document.getElementById('p_ref').value = '';
                document.getElementById('p_note').value = '';
                closePay();
                load();
            } catch (e) {
                err.textContent = 'Could not save. Please try again.';
                err.style.display = 'block';
            }
        }

        document.getElementById('billmodal').addEventListener('click', e => { if (e.target.id === 'billmodal') closeBill(); });
        document.getElementById('paymodal').addEventListener('click', e => { if (e.target.id === 'paymodal') closePay(); });

        load();
    </script>
</body>
</html>
