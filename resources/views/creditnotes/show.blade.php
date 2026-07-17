<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Credit Note CN-{{ $cn->cn_no }}</title>
    <style id="pagestyle">@page { size: A4 portrait; margin: 10mm; }</style>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, sans-serif; color: #111; background: #ececec; }
        .sheet { max-width: 800px; margin: 16px auto; background: #fff; padding: 28px 32px; border: 1px solid #ccc; }
        .topbar { max-width: 800px; margin: 12px auto 0; display: flex; gap: 8px; padding: 0 8px; flex-wrap: wrap; }
        .tbtn { flex: 1; min-width: 100px; padding: 11px; text-align: center; background: #1a1a1a; color: #fff; text-decoration: none; border: none; font-size: 13px; cursor: pointer; border-radius: 4px; }
        .tbtn.outline { background: #fff; color: #1a1a1a; border: 1px solid #1a1a1a; }
        .head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #111; padding-bottom: 12px; }
        .firm { font-size: 22px; font-weight: 700; }
        .firmmeta { font-size: 12px; color: #555; line-height: 1.5; }
        .doc { text-align: right; }
        .doc .no { font-size: 18px; font-weight: 700; }
        .party { margin: 14px 0; font-size: 13px; line-height: 1.5; }
        .party .label { font-size: 11px; font-weight: 700; color: #666; letter-spacing: 0.5px; }
        .party .pname { font-weight: 700; font-size: 15px; }
        .reason { margin: 10px 0; font-size: 14px; font-weight: 600; padding: 10px 12px; border: 1px solid #999; border-left: 4px solid #1a1a1a; background: #f7f7f7; border-radius: 4px; }
        .tablewrap { overflow-x: auto; }
        table.lines { width: 100%; border-collapse: collapse; font-size: 12.5px; margin-top: 6px; }
        table.lines th { text-align: left; border: 1px solid #888; border-top: 1px solid #111; border-bottom: 1px solid #111; padding: 6px; font-size: 11.5px; background: #f2f2f2; }
        table.lines td { padding: 5px 6px; border: 1px solid #ccc; vertical-align: top; }
        table.lines .num { text-align: right; white-space: nowrap; }
        tr.brandrow td { font-weight: 700; background: #f2f2f2; }
        .linelist { display: none; margin-top: 8px; }
        .vline { border-bottom: 1px dashed #ccc; padding: 5px 0; font-size: 12.5px; }
        .vamt { display: flex; justify-content: space-between; margin-top: 2px; }
        .totals { margin-top: 14px; margin-left: auto; width: 300px; font-size: 13.5px; }
        .totals .grand { display: flex; justify-content: space-between; border-top: 2px solid #111; padding-top: 8px; font-size: 17px; font-weight: 700; }
        .creditnote-msg { margin-top: 16px; font-size: 13.5px; font-weight: 600; padding: 10px 12px; border: 1px solid #1e7e34; border-left: 4px solid #1e7e34; background: #e6f4ea; color: #1e7e34; border-radius: 4px; }
        .foot { margin-top: 26px; font-size: 12px; color: #444; }
        .smodal { position: fixed; inset: 0; background: rgba(0,0,0,0.55); display: flex; align-items: center; justify-content: center; padding: 16px; z-index: 50; }
        .smodal[hidden] { display: none; }
        .smodal .sbox { background: #fff; border-radius: 6px; width: 100%; max-width: 380px; padding: 16px; }
        .smodal .stitle { font-weight: 700; font-size: 16px; display: flex; justify-content: space-between; align-items: center; }
        .smodal .sx { background: none; border: none; font-size: 24px; cursor: pointer; line-height: 1; }
        .smodal label { display: block; font-size: 13px; margin: 12px 0 4px; font-weight: 600; }
        .smodal input { width: 100%; padding: 10px; font-size: 15px; border: 1px solid #aaa; border-radius: 4px; }
        .smodal .err { color: #b00020; font-size: 12.5px; margin-top: 6px; display: none; }
        .smodal .sgo { width: 100%; margin-top: 14px; padding: 12px; background: #1a1a1a; color: #fff; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; }
        @media (max-width: 640px) {
            .sheet { margin: 8px; padding: 16px 12px; }
            .head { flex-direction: column; gap: 8px; }
            .doc { text-align: left; }
            .tablewrap { display: none; }
            .linelist { display: block; }
            .totals { width: 100%; }
        }
        @media print {
            body { background: #fff; }
            .sheet { max-width: none; margin: 0; border: 1.5px solid #111; padding: 5mm; }
            .topbar { display: none; }
            .tablewrap { display: block !important; }
            .linelist { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <button type="button" class="tbtn" onclick="window.print()">PRINT</button>
        <a class="tbtn" href="{{ $pdfUrl }}">DOWNLOAD PDF</a>
        @if (!$public)
            <button type="button" class="tbtn" onclick="openShare()">WHATSAPP</button>
            <button type="button" class="tbtn" id="copylink" onclick="copyLink()">COPY LINK</button>
            <a class="tbtn outline" href="{{ route('ledger.show', $cn->partner) }}">LEDGER</a>
            <a class="tbtn outline" href="{{ route('products.index') }}">HOME</a>
        @endif
    </div>

    <div class="sheet">
        <div class="head">
            <div>
                <div class="firm">{{ $s['firm_name'] ?? 'Credit Note' }}</div>
                <div class="firmmeta">
                    @if (!empty($s['firm_gst'])) GSTIN: {{ $s['firm_gst'] }}<br>@endif
                    Mobile: {{ $s['firm_mobile'] ?? '' }}@if (!empty($s['firm_alt_mobile'])), {{ $s['firm_alt_mobile'] }}@endif
                    @if (!empty($s['firm_address']))<br>{{ $s['firm_address'] }}@endif
                </div>
            </div>
            <div class="doc">
                <div style="font-size:12px; letter-spacing:1px; color:#555;">CREDIT NOTE</div>
                <div class="no">CN-{{ $cn->cn_no }}</div>
                <div style="font-size:13px;">{{ $cn->cn_date->format('d M Y') }}</div>
            </div>
        </div>

        <div class="party">
            <div class="label">CREDITED TO</div>
            <div class="pname">{{ $cn->partner->firm_name }}</div>
            <div>Mobile: {{ $cn->partner->mobile }}</div>
            @if ($cn->partner->gst_number)<div>GSTIN: {{ $cn->partner->gst_number }}</div>@endif
        </div>

        @if ($cn->reason)
            <div class="reason">Reason: {{ $cn->reason }}</div>
        @endif

        @if ($cn->kind === 'goods')
            <div class="tablewrap">
                <table class="lines">
                    <thead>
                        <tr>
                            <th style="width:26px;">#</th>
                            <th>Returned item</th>
                            <th class="num" style="width:64px;">MRP</th>
                            <th class="num" style="width:44px;">Qty</th>
                            <th class="num" style="width:70px;">Rate</th>
                            <th class="num" style="width:84px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sn = 0; $curBrand = null; @endphp
                        @foreach ($cn->lines as $line)
                            @if ($line->brand !== $curBrand)
                                @php $curBrand = $line->brand; @endphp
                                <tr class="brandrow"><td colspan="6">{{ $line->brand }}</td></tr>
                            @endif
                            @php $sn++; @endphp
                            <tr>
                                <td>{{ $sn }}</td>
                                <td>{{ $line->name }}</td>
                                <td class="num">{{ number_format($line->mrp, 2) }}</td>
                                <td class="num">{{ $line->qty }}</td>
                                <td class="num">{{ number_format($line->rate, 2) }}</td>
                                <td class="num">{{ number_format($line->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="linelist">
                @php $vn = 0; @endphp
                @foreach ($cn->lines as $line)
                    <div class="vline">
                        <div style="font-weight:600;">{{ ++$vn }}. {{ $line->name }}</div>
                        <div class="vamt">
                            <span>{{ $line->qty }} &times; {{ number_format($line->rate, 2) }}</span>
                            <b>{{ number_format($line->amount, 2) }}</b>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="totals">
            <div class="grand"><span>AMOUNT CREDITED</span><span>Rs {{ number_format($cn->total, 2) }}</span></div>
        </div>

        <div class="creditnote-msg">
            Amount credited to the account of {{ $cn->partner->firm_name }}. Outstanding balance payable to {{ $s['firm_name'] ?? 'us' }} stands reduced by this amount.
        </div>

        <div class="foot">
            @if ($cn->kind === 'goods')
                Returned units added back to stock: {{ $cn->lines->sum('qty') }}
            @else
                Amount-only credit note (no goods returned).
            @endif
        </div>
    </div>

    @if (!$public)
        <div id="sharemodal" class="smodal" hidden>
            <div class="sbox">
                <div class="stitle">
                    <span>Send credit note on WhatsApp</span>
                    <button type="button" class="sx" onclick="closeShare()">&times;</button>
                </div>
                <label for="wanum">Receiver mobile number</label>
                <input type="tel" id="wanum" inputmode="numeric" maxlength="10" value="{{ $cn->partner->mobile }}">
                <div class="err" id="waerr">Enter a valid 10-digit mobile number.</div>
                <button type="button" class="sgo" onclick="sendWhatsApp()">OPEN WHATSAPP</button>
            </div>
        </div>
    @endif

    <script>
        @if (!$public)
        const WA_TEXT = @json($waText);
        const PUBLIC_URL = @json($publicUrl);

        function openShare() { document.getElementById('sharemodal').hidden = false; document.getElementById('wanum').focus(); }
        function closeShare() { document.getElementById('sharemodal').hidden = true; }

        function sendWhatsApp() {
            const num = document.getElementById('wanum').value.trim();
            const err = document.getElementById('waerr');
            if (!/^\d{10}$/.test(num)) { err.style.display = 'block'; return; }
            err.style.display = 'none';
            closeShare();
            window.open('https://wa.me/91' + num + '?text=' + encodeURIComponent(WA_TEXT), '_blank');
        }

        function copyLink() {
            const done = () => {
                const b = document.getElementById('copylink');
                const t = b.textContent;
                b.textContent = 'COPIED \u2713';
                setTimeout(() => { b.textContent = t; }, 1400);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(PUBLIC_URL).then(done);
            } else {
                const ta = document.createElement('textarea');
                ta.value = PUBLIC_URL;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                done();
            }
        }

        document.getElementById('sharemodal').addEventListener('click', e => { if (e.target.id === 'sharemodal') closeShare(); });
        @endif
    </script>
</body>
</html>
