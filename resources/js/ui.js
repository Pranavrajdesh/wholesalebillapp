// wholesaleBillApp shared UI helpers
// Exposed on window.UI by app.js — usable from inline page scripts after DOMContentLoaded.

function esc(s) {
    const d = document.createElement('div');
    d.textContent = s ?? '';
    return d.innerHTML;
}

// Enhances a native <select> into a type-to-filter searchable dropdown.
// The native select stays the source of truth: picking an item sets select.value
// and dispatches 'change', so existing handlers keep working.
export function comboSelect(select, opts = {}) {
    const wrap = document.createElement('div');
    wrap.className = 'combo';
    select.style.display = 'none';
    select.insertAdjacentElement('afterend', wrap);

    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'combo-input';
    input.placeholder = opts.placeholder || 'Type to search';
    input.autocomplete = 'off';

    const list = document.createElement('div');
    list.className = 'combo-list';
    list.hidden = true;

    wrap.append(input, list);

    const options = () => [...select.options].map(o => ({ value: o.value, label: o.textContent }));

    function render(filter = '') {
        const f = filter.trim().toLowerCase();
        const shown = options().filter(o => !f || o.label.toLowerCase().includes(f));
        list.innerHTML = shown.length
            ? shown.map(o => '<div class="combo-item" data-value="' + esc(o.value) + '">' + esc(o.label) + '</div>').join('')
            : '<div class="combo-empty muted">No match</div>';
        list.hidden = false;
    }

    function syncLabel() {
        const sel = select.options[select.selectedIndex];
        input.value = sel && sel.value !== '' ? sel.textContent : '';
    }

    function pick(item) {
        select.value = item.dataset.value;
        list.hidden = true;
        select.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function move(delta) {
        const items = [...list.querySelectorAll('.combo-item')];
        if (!items.length) return;
        let idx = items.findIndex(el => el.classList.contains('kbd-active'));
        idx = Math.max(0, Math.min(items.length - 1, idx + delta));
        items.forEach((el, i) => el.classList.toggle('kbd-active', i === idx));
        items[idx].scrollIntoView({ block: 'nearest' });
    }

    input.addEventListener('focus', () => { input.select(); render(''); });
    input.addEventListener('input', () => render(input.value));

    input.addEventListener('keydown', e => {
        if (e.key === 'ArrowDown') { e.preventDefault(); if (list.hidden) render(input.value); move(1); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); move(-1); }
        else if (e.key === 'Enter') {
            e.preventDefault();
            if (list.hidden) return;
            const active = list.querySelector('.kbd-active') || list.querySelector('.combo-item');
            if (active) pick(active);
        }
        else if (e.key === 'Escape') { list.hidden = true; syncLabel(); }
    });

    list.addEventListener('click', e => {
        const item = e.target.closest('.combo-item');
        if (item) pick(item);
    });

    document.addEventListener('click', e => {
        if (!wrap.contains(e.target)) { list.hidden = true; syncLabel(); }
    });

    select.addEventListener('change', syncLabel);

    syncLabel();
    return { syncLabel };
}

// Floating "back to top" button, appears after scrolling down.
export function initGoTop() {
    const btn = document.createElement('button');
    btn.className = 'gotop';
    btn.type = 'button';
    btn.innerHTML = '&uarr; TOP';
    btn.hidden = true;
    document.body.appendChild(btn);

    window.addEventListener('scroll', () => {
        btn.hidden = window.scrollY < 400;
    }, { passive: true });

    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

// Arrow-key highlight for suggestion dropdowns.
// Does NOT handle Enter — the page's own Enter handler should check for
// a '.kbd-active' item and pick it, else run its default action.
export function listKeyNav(input, container, itemSelector) {
    let idx = -1;

    function mark() {
        const items = [...container.querySelectorAll(itemSelector)];
        items.forEach((el, i) => el.classList.toggle('kbd-active', i === idx));
        if (idx >= 0 && items[idx]) items[idx].scrollIntoView({ block: 'nearest' });
    }

    input.addEventListener('keydown', e => {
        const items = [...container.querySelectorAll(itemSelector)];
        if (container.hidden || !items.length) { idx = -1; return; }
        if (e.key === 'ArrowDown') { e.preventDefault(); idx = Math.min(idx + 1, items.length - 1); mark(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); idx = Math.max(idx - 1, 0); mark(); }
        else if (e.key === 'Escape') { idx = -1; }
    });

    return {
        reset() { idx = -1; },
        active() { return container.querySelector(itemSelector + '.kbd-active'); },
    };
}
