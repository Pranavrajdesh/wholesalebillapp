@extends('layouts.app')

@section('title', 'Stock Inward')

@section('content')
    <h2>Stock Inward</h2>
    <hr class="rule">

    <div style="margin:14px 0;">
        <a class="btn" href="{{ route('inward.create') }}">+ NEW INWARD ENTRY</a>
    </div>

    <hr class="rule">
    <p id="icount" class="count"></p>

    <div id="ilist"></div>
    <p id="iloading" class="muted" hidden>Loading&hellip;</p>
    <p id="iempty" class="muted" hidden>No inward entries yet.</p>
    <button id="iloadmore" class="btn btn-outline" hidden>LOAD MORE</button>

    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const B = window.Billing;
        const dataUrl = "{{ route('inward.data') }}";
        const state = { offset: 0 };

        const $ = id => document.getElementById(id);

        function cardHtml(e) {
            return '<div class="card" style="margin-bottom:10px;">'
                + '<div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">'
                + '<div>'
                + '<div style="font-weight:700;">' + B.esc(e.date) + (e.supplier ? ' &mdash; ' + B.esc(e.supplier) : '') + '</div>'
                + '<div style="color:#444; font-size:13px;">' + e.line_count + ' item' + (e.line_count === 1 ? '' : 's') + '</div>'
                + (e.note ? '<div style="margin-top:6px; font-size:13.5px; font-weight:600; color:#1a1a1a; padding:8px 10px; border:1px solid #999; border-left:4px solid #1a1a1a; background:#f7f7f7; border-radius:4px;">NOTE: ' + B.esc(e.note) + '</div>' : '')
                + '</div>'
                + '</div>'
                + '<div style="margin-top:10px;">'
                + '<a class="btn" href="' + e.url + '">VIEW</a>'
                + '</div>'
                + '</div>';
        }

        async function fetchList(reset) {
            if (reset) { state.offset = 0; $('ilist').innerHTML = ''; }
            $('iloading').hidden = false;
            $('iempty').hidden = true;
            const res = await fetch(dataUrl + '?' + new URLSearchParams({ offset: state.offset, limit: 25 }));
            const data = await res.json();
            $('iloading').hidden = true;
            data.items.forEach(i => $('ilist').insertAdjacentHTML('beforeend', cardHtml(i)));
            state.offset = data.next_offset;
            $('iloadmore').hidden = !data.has_more;
            let endnote = document.getElementById('endnote');
            if (!endnote) { endnote = document.createElement('p'); endnote.id = 'endnote'; endnote.className = 'endnote'; endnote.textContent = '\u2014 End of list \u2014'; $('iloadmore').insertAdjacentElement('afterend', endnote); }
            endnote.hidden = data.has_more || data.total === 0;
            $('iempty').hidden = $('ilist').children.length > 0;
            $('icount').textContent = data.total + (data.total === 1 ? ' entry' : ' entries');
        }

        $('iloadmore').addEventListener('click', () => fetchList(false));
        fetchList(true);
    });
    </script>
@endsection
