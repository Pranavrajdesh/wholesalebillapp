@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
    <h2>Invoices</h2>
    <hr class="rule">

    <div class="searchwrap">
        <input type="text" id="search" placeholder="Search by partner or INV number" autocomplete="off">
        <button type="button" id="clearsearch" class="xbtn" hidden>&times;</button>
    </div>

    <details class="filterbox">
        <summary>FILTERS</summary>
        <label for="f_from">From date</label>
        <input type="date" id="f_from">
        <label for="f_to">To date</label>
        <input type="date" id="f_to">
    </details>

    <hr class="rule">
    <p id="count" class="count"></p>

    <div id="list"></div>
    <p id="loading" class="muted" hidden>Loading&hellip;</p>
    <p id="empty" class="muted" hidden>No invoices found.</p>
    <button id="loadmore" class="btn btn-outline" hidden>LOAD MORE</button>

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const B = window.Billing;
        const dataUrl = "{{ route('invoices.data') }}";

        const state = { q: '', from: '', to: '', offset: 0 };
        const list = document.getElementById('list');
        const loading = document.getElementById('loading');
        const empty = document.getElementById('empty');
        const loadmore = document.getElementById('loadmore');
        const count = document.getElementById('count');
        const search = document.getElementById('search');
        const clearbtn = document.getElementById('clearsearch');
        const fromEl = document.getElementById('f_from');
        const toEl = document.getElementById('f_to');
        let timer = null;

        UI.initGoTop();

        function cardHtml(i) {
            return '<div class="card" style="margin-bottom:10px;">'
                + '<div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">'
                + '<div>'
                + '<div style="font-weight:700;">INV-' + i.invoice_no + '</div>'
                + '<div class="muted">' + B.esc(i.date) + '</div>'
                + '</div>'
                + '<div style="text-align:right;">'
                + '<div style="font-weight:700;">' + B.money(i.total) + '</div>'
                + '<div class="muted">' + i.line_count + ' item' + (i.line_count === 1 ? '' : 's') + '</div>'
                + '</div>'
                + '</div>'
                + '<div style="margin-top:4px;">' + B.esc(i.firm_name) + '</div>'
                + '<div style="margin-top:10px;">'
                + '<a class="btn" href="' + i.url + '">OPEN</a>'
                + '</div>'
                + '</div>';
        }

        function params(extra) {
            const p = new URLSearchParams();
            if (state.q) p.set('q', state.q);
            if (state.from) p.set('from', state.from);
            if (state.to) p.set('to', state.to);
            Object.entries(extra || {}).forEach(([k, v]) => p.set(k, v));
            return p.toString();
        }

        async function fetchList(reset) {
            if (reset) { state.offset = 0; list.innerHTML = ''; }
            loading.hidden = false;
            empty.hidden = true;
            const res = await fetch(dataUrl + '?' + params({ offset: state.offset, limit: 25 }));
            const data = await res.json();
            loading.hidden = true;
            data.items.forEach(i => list.insertAdjacentHTML('beforeend', cardHtml(i)));
            state.offset = data.next_offset;
            loadmore.hidden = !data.has_more;
            let endnote = document.getElementById('endnote');
            if (!endnote) { endnote = document.createElement('p'); endnote.id = 'endnote'; endnote.className = 'endnote'; endnote.textContent = '\u2014 End of list \u2014'; loadmore.insertAdjacentElement('afterend', endnote); }
            endnote.hidden = data.has_more || data.total === 0;
            empty.hidden = list.children.length > 0;
            count.textContent = data.total + (data.total === 1 ? ' invoice' : ' invoices');
        }

        search.addEventListener('input', () => {
            clearTimeout(timer);
            clearbtn.hidden = search.value === '';
            timer = setTimeout(() => {
                state.q = search.value.trim();
                fetchList(true);
            }, 300);
        });

        clearbtn.addEventListener('click', () => {
            search.value = '';
            clearbtn.hidden = true;
            state.q = '';
            fetchList(true);
            search.focus();
        });

        fromEl.addEventListener('change', () => { state.from = fromEl.value; fetchList(true); });
        toEl.addEventListener('change', () => { state.to = toEl.value; fetchList(true); });
        loadmore.addEventListener('click', () => fetchList(false));

        fetchList(true);
    });
    </script>
@endsection
