{{-- Ledger-style topbar for report pages. Include directly after the width override. --}}
<style>
    header.app, .navbar { display: none; }
    .rtopbar { display: flex; gap: 8px; margin: 12px 0 4px; flex-wrap: wrap; }
    .rtopbar .tbtn { flex: 1; min-width: 142px; padding: 11px; text-align: center; background: #1a1a1a; color: #fff; text-decoration: none; border: none; font-size: 13px; cursor: pointer; border-radius: 4px; }
    .rtopbar .tbtn.outline { background: #fff; color: #1a1a1a; border: 1px solid #1a1a1a; }
    .rmodal { position: fixed; inset: 0; background: rgba(0,0,0,0.55); display: flex; align-items: center; justify-content: center; padding: 16px; z-index: 50; }
    .rmodal[hidden] { display: none !important; }
    .rmodal .rbox { background: #fff; border-radius: 6px; width: 100%; max-width: 380px; padding: 16px; max-height: 90vh; overflow: auto; overscroll-behavior: contain; }
    .rmodal .rtitle { font-weight: 700; font-size: 16px; display: flex; justify-content: space-between; align-items: center; }
    .rmodal .rx { background: none; border: none; font-size: 24px; cursor: pointer; line-height: 1; }
    .rmodal .rlinks { display: flex; flex-direction: column; gap: 8px; margin-top: 14px; }
    .rmodal .rlinks a { padding: 11px; text-align: center; background: #1a1a1a; color: #fff; text-decoration: none; font-size: 13px; border-radius: 4px; }
    .rmodal .rlinks a.outline { background: #fff; color: #1a1a1a; border: 1px solid #1a1a1a; }
    @media print { .rtopbar, .rmodal { display: none !important; } }
</style>

<div class="rtopbar">
    <a class="tbtn outline" href="{{ route('products.index') }}">HOME</a>
    <a class="tbtn outline" href="{{ route('dashboard') }}">DASHBOARD</a>
    <button type="button" class="tbtn" onclick="document.getElementById('reportsmodal').hidden = false">&#9776; MORE REPORTS</button>
</div>

<div id="reportsmodal" class="rmodal" hidden>
    <div class="rbox">
        <div class="rtitle">
            <span>Reports</span>
            <button type="button" class="rx" onclick="document.getElementById('reportsmodal').hidden = true">&times;</button>
        </div>
        <div class="rlinks">
            <a href="{{ route('reports.sales_register') }}">SALES REGISTER</a>
            <a href="{{ route('reports.sales_summary') }}">SALES SUMMARY</a>
            <a href="{{ route('reports.collections') }}">COLLECTIONS REGISTER</a>
            <a href="{{ route('reports.outstanding') }}">OUTSTANDING &amp; AGING</a>
            <a href="{{ route('reports.stock') }}">STOCK REPORT</a>
            <a href="{{ route('reports.purchases') }}">PURCHASE REGISTER</a>
            <a href="{{ route('reports.gst') }}">GST / HSN SUMMARY</a>
            <a class="outline" href="{{ route('reports.index') }}">ALL REPORTS PAGE</a>
        </div>
    </div>
</div>

<script>
    document.getElementById('reportsmodal').addEventListener('click', function (e) {
        if (e.target.id === 'reportsmodal') this.hidden = true;
    });
</script>
