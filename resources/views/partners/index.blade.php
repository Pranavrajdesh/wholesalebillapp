@extends('layouts.app')

@section('title', 'Partners')

@section('content')
    <h2>Partners</h2>
    <hr class="rule">

    <div class="searchwrap">
        <input type="text" id="psearch" placeholder="Search firm, contact or mobile" autocomplete="off">
        <button type="button" id="pclear" class="xbtn" hidden>&times;</button>
        <div id="psug" class="suggest" hidden></div>
    </div>

    <div style="margin:14px 0;">
        <a class="btn" href="{{ route('partners.create') }}">+ NEW PARTNER</a>
    </div>

    <hr class="rule">
    <p id="pcount" class="count"></p>

    <div id="plist"></div>
    <p id="ploading" class="muted" hidden>Loading&hellip;</p>
    <p id="pempty" class="muted" hidden>No partners found.</p>
    <button id="ploadmore" class="btn btn-outline" hidden>LOAD MORE</button>

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const dataUrl = "{{ route('partners.data') }}";

        const state = { q: '', offset: 0 };
        const list = document.getElementById('plist');
        const loading = document.getElementById('ploading');
        const empty = document.getElementById('pempty');
        const loadmore = document.getElementById('ploadmore');
        const count = document.getElementById('pcount');
        const search = document.getElementById('psearch');
        const clearbtn = document.getElementById('pclear');
        const sug = document.getElementById('psug');
        const nav = UI.listKeyNav(search, sug, '.sug-item');
        let sugTimer = null;

        function esc(s) {
            const d = document.createElement('div');
            d.textContent = s ?? '';
            return d.innerHTML;
        }

        const inr = n => 'Rs ' + Math.abs(n).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function cardHtml(p) {
            const badges = (p.is_active ? '' : '<span class="badge-red">INACTIVE</span> ')
                + (p.portal_access ? '' : '<span class="badge-amber">PORTAL OFF</span>');
            return '<div class="card">'
                + '<div style="display:flex; justify-content:space-between; gap:8px; align-items:flex-start;">'
                + '<div style="font-weight:700; font-size:15px;">' + esc(p.firm_name) + '</div>'
                + (badges ? '<div style="white-space:nowrap;">' + badges + '</div>' : '')
                + '</div>'
                + (p.contact_name ? '<div style="color:#444; font-size:13px;">' + esc(p.contact_name) + '</div>' : '')
                + '<div style="font-size:13.5px; margin-top:2px;"><a href="tel:+91' + esc(p.mobile) + '" style="color:#1a1a1a; font-weight:600; text-decoration:none;">' + esc(p.mobile) + '</a></div>'
                + '<div style="margin-top:8px; font-size:13.5px;">'
                + (p.balance > 0
                    ? '<span class="bal-due">Due: ' + inr(p.balance) + '</span>'
                    : (p.balance < 0
                        ? '<span class="bal-adv">Advance: ' + inr(p.balance) + '</span>'
                        : '<span class="bal-ok">&#10003; Settled</span>'))
                + '</div>'
                + '<div style="margin-top:10px; display:flex; gap:8px;">'
                + '<a class="btn cardbtn" href="' + p.ledger_url + '" style="text-decoration:none;">LEDGER</a>'
                + '<a class="btn btn-outline cardbtn" href="' + p.edit_url + '" style="text-decoration:none;">EDIT</a>'
                + '</div>'
                + '</div>';
        }

        async function fetchList(reset) {
            if (reset) { state.offset = 0; list.innerHTML = ''; }
            loading.hidden = false;
            empty.hidden = true;
            const p = new URLSearchParams({ status: 'all', offset: state.offset, limit: 25 });
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
            count.textContent = data.total + (data.total === 1 ? ' partner' : ' partners');
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
                const res = await fetch(dataUrl + '?' + new URLSearchParams({ q: term, limit: 6, status: 'all' }));
                const data = await res.json();
                if (!data.items.length) { sug.hidden = true; nav.reset(); return; }
                sug.innerHTML = data.items.map(p =>
                    '<div class="sug-item" data-name="' + esc(p.firm_name) + '">'
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
                if (active) {
                    search.value = active.dataset.name;
                    state.q = active.dataset.name;
                    sug.hidden = true;
                    nav.reset();
                    fetchList(true);
                    return;
                }
                state.q = search.value.trim();
                sug.hidden = true;
                fetchList(true);
            }
            if (e.key === 'Escape') sug.hidden = true;
        });

        sug.addEventListener('click', e => {
            const item = e.target.closest('.sug-item');
            if (!item) return;
            search.value = item.dataset.name;
            state.q = item.dataset.name;
            sug.hidden = true;
            nav.reset();
            fetchList(true);
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
