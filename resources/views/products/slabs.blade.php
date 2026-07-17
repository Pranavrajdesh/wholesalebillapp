@extends('layouts.app')

@section('title', 'Rates')

@section('content')
    <h2>Rate by Quantity</h2>
    <hr class="rule">
    <p class="crumbs"><a href="{{ route('products.index') }}">&larr; Back to Product List</a></p>

    <div class="card">
        <div class="prow">
            @if ($product->image_path)
                <img class="pimg" src="{{ asset('storage/' . $product->image_path) }}" alt="">
            @else
                <div class="pimg">{{ $product->initials() }}</div>
            @endif
            <div>
                <div style="font-weight:600;">{{ $product->name }}</div>
                <div class="muted">{{ $product->brand->name }} &middot; {{ $product->category->name }}</div>
                <div class="muted">MRP &#8377;{{ number_format($product->mrp, 2) }}</div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('products.slabs.update', $product) }}">
        @csrf
        @method('PUT')

        <label>Slabs &mdash; buying this many or more gets this rate, scheme and offer</label>
        <div id="slabrows"></div>

        <div style="margin:10px 0;">
            <button type="button" id="addslab" class="btn btn-outline">+ ADD SLAB</button>
        </div>

        <div style="margin-top:14px;">
            <button class="btn" type="submit">SAVE RATES</button>
        </div>
    </form>
    <hr class="rule">

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const rows = document.getElementById('slabrows');
        let idx = 0;
        const MRP = {{ (float) $product->mrp }};

        @php
            $slabData = $slabs->map(fn ($s) => [
                'min_qty' => $s->min_qty,
                'rate' => (float) $s->rate,
                'scheme_percent' => (float) $s->scheme_percent,
                'offer_buy_qty' => $s->offer_buy_qty,
                'offer_free_qty' => $s->offer_free_qty,
            ])->values();
        @endphp
        const existing = @json($slabData);

        function addRow(s) {
            s = s || {};
            const div = document.createElement('div');
            div.className = 'slabcard';
            div.innerHTML =
                '<div class="slabhead">'
                + '<span class="slabtag"></span>'
                + '<span class="slab-label">&mdash;</span>'
                + '<span class="offerlabel muted"></span>'
                + '<button type="button" class="slab-remove xbtn" title="Remove">&times;</button>'
                + '</div>'
                + '<div class="slabrow">'
                + '<div class="fld"><span>Min qty</span><input type="number" name="slabs[' + idx + '][min_qty]" min="1" step="1" value="' + (s.min_qty ?? '') + '"></div>'
                + '<div class="fld"><span>Rate &#8377;</span><input type="number" name="slabs[' + idx + '][rate]" min="0.01" step="0.01" value="' + (s.rate ?? '') + '"></div>'
                + '<div class="fld"><span>Factor</span><input type="number" class="factor-input" min="0.01" step="0.01" title="Rate = MRP / factor" value="' + (s.rate ? (MRP / s.rate).toFixed(2) : '') + '"></div>'
                + '<div class="fld"><span>Flat %</span><input type="number" class="flat-input" step="0.01" title="Rate = MRP minus this percent" value="' + (s.rate ? ((1 - s.rate / MRP) * 100).toFixed(1) : '') + '"></div>'
                + '</div>'
                + '<div class="slabrow sub">'
                + '<div class="fld"><span>Scheme %</span><input type="number" name="slabs[' + idx + '][scheme_percent]" min="0" max="100" step="0.01" value="' + (s.scheme_percent ? s.scheme_percent : '') + '"></div>'
                + '<div class="fld"><span>Net flat %</span><input type="number" class="netflat-input" step="0.01" title="Total % off MRP including scheme" value="' + (s.rate ? ((1 - (s.rate * (1 - (s.scheme_percent || 0) / 100)) / MRP) * 100).toFixed(1) : '') + '"></div>'
                + '<div class="fld"><span>Buy</span><input type="number" name="slabs[' + idx + '][offer_buy_qty]" min="1" step="1" value="' + (s.offer_buy_qty ?? '') + '"></div>'
                + '<div class="fld"><span>Free</span><input type="number" name="slabs[' + idx + '][offer_free_qty]" min="1" step="1" value="' + (s.offer_free_qty ?? '') + '"></div>'
                + '</div>'
                + '<div class="slabfoot muted"></div>';
            rows.appendChild(div);
            idx++;
            recompute();
        }

        function recompute() {
            const cards = [...rows.querySelectorAll('.slabcard')];
            const mins = cards
                .map(c => parseInt(c.querySelector('input[name*="min_qty"]').value, 10))
                .filter(n => !isNaN(n) && n >= 1)
                .sort((a, b) => a - b);

            cards.forEach((c, i) => { c.querySelector('.slabtag').textContent = 'SLAB ' + (i + 1); });

            cards.forEach(c => {
                const min = parseInt(c.querySelector('input[name*="min_qty"]').value, 10);
                const label = c.querySelector('.slab-label');
                const offerLabel = c.querySelector('.offerlabel');
                const buy = parseInt(c.querySelector('input[name*="offer_buy_qty"]').value, 10);
                const free = parseInt(c.querySelector('input[name*="offer_free_qty"]').value, 10);

                offerLabel.textContent = (!isNaN(buy) && !isNaN(free)) ? buy + '+' + free : '';

                const rate = parseFloat(c.querySelector('input[name*="[rate]"]').value);
                const scheme = parseFloat(c.querySelector('input[name*="scheme_percent"]').value) || 0;
                const foot = c.querySelector('.slabfoot');
                if (!isNaN(rate) && rate > 0) {
                    const net = rate * (1 - scheme / 100);
                    if (!isNaN(buy) && !isNaN(free) && buy > 0) {
                        const landing = (buy * net) / (buy + free);
                        foot.innerHTML = 'Landing cost: <b>\u20B9' + landing.toFixed(2) + '</b>/unit at ' + buy + '+' + free
                            + ' &middot; margin <b>' + (MRP / landing).toFixed(2) + '</b>';
                    } else {
                        foot.innerHTML = 'Landing cost: <b>\u20B9' + net.toFixed(2) + '</b>/unit'
                            + (scheme > 0 ? ' (after ' + scheme + '% scheme)' : '')
                            + ' &middot; margin <b>' + (MRP / net).toFixed(2) + '</b>';
                    }
                } else {
                    foot.textContent = '';
                }

                if (isNaN(min) || min < 1) { label.innerHTML = '&mdash;'; return; }
                const next = mins.find(m => m > min);
                label.textContent = next ? (min === next - 1 ? String(min) : min + '\u2013' + (next - 1)) : min + '+';
            });
        }

        rows.addEventListener('input', e => {
            const t = e.target;
            const card = t.closest('.slabcard');

            function syncDerived(skip) {
                const rateInput = card.querySelector('input[name*="[rate]"]');
                const r = parseFloat(rateInput.value);
                const fInput = card.querySelector('.factor-input');
                const flInput = card.querySelector('.flat-input');
                if (skip !== 'factor' && fInput) fInput.value = (!isNaN(r) && r > 0) ? (MRP / r).toFixed(2) : '';
                if (skip !== 'flat' && flInput) flInput.value = (!isNaN(r) && r > 0) ? ((1 - r / MRP) * 100).toFixed(1) : '';
                const nfInput = card.querySelector('.netflat-input');
                const sch = parseFloat(card.querySelector('input[name*="scheme_percent"]').value) || 0;
                if (skip !== 'netflat' && nfInput) nfInput.value = (!isNaN(r) && r > 0) ? ((1 - (r * (1 - sch / 100)) / MRP) * 100).toFixed(1) : '';
            }

            if (t.classList.contains('factor-input')) {
                const f = parseFloat(t.value);
                if (!isNaN(f) && f > 0) card.querySelector('input[name*="[rate]"]').value = (MRP / f).toFixed(2);
                syncDerived('factor');
                recompute();
                return;
            }

            if (t.classList.contains('flat-input')) {
                const fl = parseFloat(t.value);
                if (!isNaN(fl) && fl < 100) card.querySelector('input[name*="[rate]"]').value = (MRP * (1 - fl / 100)).toFixed(2);
                syncDerived('flat');
                recompute();
                return;
            }

            if (t.classList.contains('netflat-input')) {
                const nf = parseFloat(t.value);
                const sch2 = parseFloat(card.querySelector('input[name*="scheme_percent"]').value) || 0;
                if (!isNaN(nf) && nf < 100 && sch2 < 100) {
                    card.querySelector('input[name*="[rate]"]').value = (MRP * (1 - nf / 100) / (1 - sch2 / 100)).toFixed(2);
                }
                syncDerived('netflat');
                recompute();
                return;
            }

            if (!t.name) return;

            if (t.name.includes('[rate]')) {
                syncDerived(null);
                recompute();
            }

            if (t.name.includes('min_qty') || t.name.includes('offer_') || t.name.includes('scheme_percent')) {
                if (t.name.includes('scheme_percent')) syncDerived(null);
                recompute();
            }
        });

        rows.addEventListener('click', e => {
            if (e.target.closest('.slab-remove')) {
                e.target.closest('.slabcard').remove();
                recompute();
            }
        });

        document.getElementById('addslab').addEventListener('click', () => addRow());

        if (existing.length) {
            existing.forEach(s => addRow(s));
        } else {
            addRow();
        }
    });
    </script>
@endsection
