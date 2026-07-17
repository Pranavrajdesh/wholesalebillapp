@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
    <h2>Suppliers</h2>
    <hr class="rule">

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    <div class="searchwrap">
        <input type="text" id="ssearch" placeholder="Search firm, contact or mobile" autocomplete="off">
        <button type="button" id="sclear" class="xbtn" hidden>&times;</button>
        <div id="ssug" class="suggest" hidden></div>
    </div>

    <div style="margin:14px 0;">
        <a class="btn" href="{{ route('suppliers.create') }}">+ ADD SUPPLIER</a>
    </div>

    <hr class="rule">
    <p id="scount" class="count"></p>

    <div id="slist"></div>
    <p id="sloading" class="muted" hidden>Loading&hellip;</p>
    <p id="sempty" class="muted" hidden>No suppliers found.</p>
    <button id="sloadmore" class="btn btn-outline" hidden>LOAD MORE</button>

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const dataUrl = "{{ route('suppliers.data') }}";

        const state = { q: '', offset: 0 };
        const list = document.getElementById('slist');
        const loading = document.getElementById('sloading');
        const empty = document.getElementById('sempty');
        const loadmore = document.getElementById('sloadmore');
        const count = document.getElementById('scount');
        const search = document.getElementById('ssearch');
        const clearbtn = document.getElementById('sclear');
        const sug = document.getElementById('ssug');
        const nav = UI.listKeyNav(search, sug, '.sug-item');
        let sugTimer = null;

        function esc(s) {
            const d = document.createElement('div');
            d.textContent = s ?? '';
            return d.innerHTML;
        }

        function cardHtml(s) {
            return '<div class="card">'
                + '<div style="display:flex; justify-content:space-between; gap:8px; align-items:flex-start;">'
                + '<div style="font-weight:700; font-size:15px;">' + esc(s.firm_name) + '</div>'
                + (s.is_active ? '' : '<span class="badge-red">INACTIVE</span>')
                + '</div>'
                + (s.contact_name ? '<div style="color:#444; font-size:13px;">' + esc(s.contact_name) + '</div>' : '')
                + '<div style="font-size:13.5px; margin-top:2px;"><a href="tel:+91' + esc(s.mobile) + '" style="color:#1a1a1a; font-weight:600; text-decoration:none;">' + esc(s.mobile) + '</a></div>'
                + (s.gst_number ? '<div style="font-size:13px; font-weight:600; color:#1a1a1a;">GSTIN: ' + esc(s.gst_number) + '</div>' : '')
                + '<div style="margin-top:10px; display:flex; gap:8px;">'
                + '<a class="btn cardbtn" href="' + s.ledger_url + '" style="text-decoration:none;">LEDGER</a>'
                + '<a class="btn btn-outline cardbtn" href="' + s.edit_url + '" style="text-decoration:none;">EDIT</a>'
                + '</div>'
                + '</div>';
        }

        async function fetchList(reset) {
            if (reset) { state.offset = 0; list.innerHTML = ''; }
            loading.hidden = false;
            empty.hidden = true;
            const p = new URLSearchParams({ offset: state.offset, limit: 25 });
            if (state.q) p.set('q', state.q);
            const res = await fetch(dataUrl + '?' + p);
            const data = await res.json();
            loading.hidden = true;
            data.items.forEach(i => list.insertAdjacentHTML('beforeend', cardHtml(i)));
            state.offset = data.next_offset;
            loadmore.hidden = !data.has_more;
            let endnote = document.getElementById('endnote');
            if (!endnote) { endnote = document.createElement('p'); endnote.id = 'endnote'; endnote.className = 'endnote'; endnote.textContent = '\u2014 End of list \u2014'; loadmore.insertAdjacentElement('afterend', endnote); }
            endnote.hidden = data.has_more || data.total === 0;
            empty.hidden = list.children.length > 0;
            count.textContent = data.total + (data.total === 1 ? ' supplier' : ' suppliers');
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
                sug.innerHTML = data.items.map(s =>
                    '<div class="sug-item" data-url="' + s.edit_url + '">'
                    + '<div class="sug-img">' + esc(s.initials) + '</div>'
                    + '<div><div>' + esc(s.firm_name) + '</div>'
                    + '<div class="muted">' + esc(s.mobile) + '</div></div>'
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
                if (active) { window.location.href = active.dataset.url; return; }
                state.q = search.value.trim();
                sug.hidden = true;
                fetchList(true);
            }
            if (e.key === 'Escape') sug.hidden = true;
        });

        sug.addEventListener('click', e => {
            const item = e.target.closest('.sug-item');
            if (item) window.location.href = item.dataset.url;
        });

        document.addEventListener('click', e => {
            if (!e.target.closest('.searchwrap')) sug.hidden = true;
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
