// wholesaleBillApp shared billing kit
// Exposed on window.Billing by app.js.
// Depends on window.UI (listKeyNav) and a composer DOM partial being present.

export const money = n => '\u20B9' + (Math.round(n * 100) / 100).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

export function esc(s) {
    const d = document.createElement('div');
    d.textContent = s ?? '';
    return d.innerHTML;
}

export function slabLabels(slabs) {
    return slabs.map((s, i) => {
        const next = slabs[i + 1];
        if (!next) return s.min_qty + '+';
        return s.min_qty === next.min_qty - 1 ? String(s.min_qty) : s.min_qty + '\u2013' + (next.min_qty - 1);
    });
}

export function resolveSlab(slabs, qty) {
    let hit = null;
    (slabs || []).forEach(s => { if (qty >= s.min_qty) hit = s; });
    return hit;
}

export const netRate = slab => slab.rate * (1 - (slab.scheme_percent || 0) / 100);

export function suggestedFree(slab, qty) {
    if (!slab || !slab.offer_buy_qty || !slab.offer_free_qty) return 0;
    return Math.floor(qty / slab.offer_buy_qty) * slab.offer_free_qty;
}

export function slabTableHtml(item) {
    if (!item.slabs || !item.slabs.length) {
        return '<div class="muted" style="margin:8px 0;">No wholesale rates set for this product.</div>';
    }
    const labels = slabLabels(item.slabs);
    const rows = item.slabs.map((s, i) =>
        '<tr data-slab="' + i + '">'
        + '<td>' + labels[i] + '</td>'
        + '<td>' + money(s.rate) + '</td>'
        + '<td>' + (s.rate > 0 ? (item.mrp / s.rate).toFixed(2) : '&mdash;') + '</td>'
        + '<td>' + (s.scheme_percent > 0 ? s.scheme_percent + '%' : '&mdash;') + '</td>'
        + '<td>' + (netRate(s) > 0 ? ((1 - netRate(s) / item.mrp) * 100).toFixed(1) + '%' : '&mdash;') + '</td>'
        + '<td>' + (s.offer_buy_qty ? s.offer_buy_qty + '+' + s.offer_free_qty : '&mdash;') + '</td>'
        + '<td>' + (netRate(s) > 0 ? (item.mrp / netRate(s)).toFixed(2) : '&mdash;') + '</td>'
        + '</tr>'
    ).join('');
    return '<table class="slabtable"><thead><tr><th>Qty</th><th>Rate</th><th>Factor</th><th>Scheme</th><th>Net flat</th><th>Offer</th><th>Margin</th></tr></thead>'
        + '<tbody>' + rows + '</tbody></table>';
}

// qty x rate, with GST added on top for exclusive-tax lines (mirrors server law)
export function lineAmount(l, qty, rate) {
    const base = (qty || 0) * (rate || 0);
    if (l && l.tax_inclusive === false && l.tax_percent > 0) {
        return Math.round(base * (1 + l.tax_percent / 100) * 100) / 100;
    }
    return base;
}

export function gstTag(l) {
    return (l && l.tax_inclusive === false && l.tax_percent > 0)
        ? ' <b style="font-size:11px;">+' + (Math.round(l.tax_percent * 100) / 100) + '% GST</b>'
        : '';
}

export function stockWarnHtml(item, qty, free) {
    if (!item || typeof item.stock_qty === 'undefined') return '';
    const need = (qty || 0) + (free || 0);
    if (need <= item.stock_qty) return '';
    return '<div class="stockwarn">Stock alert: needs ' + need + ', in stock ' + item.stock_qty + '</div>';
}

export function hintHtml(item, qty, rate, free) {
    const total = lineAmount(item, qty, rate);
    let html = qty + ' &times; ' + money(rate) + gstTag(item) + ' = <b>' + money(total) + '</b>'
        + (free > 0 ? ' &middot; +' + free + ' FREE' : '');
    if (free > 0) {
        const landing = total / (qty + free);
        html += '<div class="landcost">Landing cost: <b>' + money(landing) + '</b>/unit for ' + (qty + free) + ' units'
            + '<br>Net flat: <b>' + ((1 - landing / item.mrp) * 100).toFixed(1) + '%</b> off MRP'
            + ' &middot; margin <b>' + (item.mrp / landing).toFixed(2) + '</b></div>';
    }
    return html;
}

// ---------------------------------------------------------------
// Composer factory. Requires the _composer partial in the DOM.
// opts: { getCart, saveCart, onChange(productId) }
// Returns { open(item) }.
// Products WITHOUT slabs open in MANUAL MODE: no auto-recalc,
// rate typed by the user, save disabled until qty>=1 and rate>0.
// ---------------------------------------------------------------
export function createComposer(opts) {
    const modal = document.getElementById('composer');
    const qtyEl = document.getElementById('cmp-qty');
    const freeEl = document.getElementById('cmp-free');
    const schemeEl = document.getElementById('cmp-scheme');
    const rateEl = document.getElementById('cmp-rate');
    const derivedRow = document.getElementById('cmp-derived');
    const factorEl = document.getElementById('cmp-factor');
    const flatEl = document.getElementById('cmp-flat');
    const baseNote = document.createElement('div');
    baseNote.className = 'muted';
    baseNote.style.cssText = 'font-size:11.5px; margin-top:3px;';
    rateEl.insertAdjacentElement('afterend', baseNote);
    const amountEl = document.getElementById('cmp-amount');
    const saveBtn = document.getElementById('cmp-save');
    const removeBtn = document.getElementById('cmp-remove');
    let cmp = null;

    const getLine = id => opts.getCart().lines.find(l => l.product_id === id);

    function open(item) {
        if (!item) return;
        const manual = !item.slabs || !item.slabs.length;
        const line = getLine(item.id);
        let work;
        if (line) {
            work = { qty: line.qty, free_qty: line.free_qty, scheme_percent: line.scheme_percent, rate: line.rate, manual_rate: line.manual_rate, manual_free: line.manual_free, manual_scheme: manual };
        } else if (manual) {
            work = { qty: 1, free_qty: 0, scheme_percent: 0, rate: 0, manual_rate: true, manual_free: true, manual_scheme: true };
        } else {
            const slab = resolveSlab(item.slabs, 1);
            work = { qty: 1, free_qty: suggestedFree(slab, 1), scheme_percent: slab.scheme_percent || 0, rate: Math.round(netRate(slab) * 100) / 100, manual_rate: false, manual_free: false, manual_scheme: false };
        }
        cmp = { item, work, manual };
        {
            const s = (work.scheme_percent || 0) / 100;
            work.base = (work.rate > 0 && s < 1) ? Math.round(work.rate / (1 - s) * 100) / 100 : work.rate;
        }

        document.getElementById('cmp-title').textContent = line ? 'Update Item' : 'Add Item';
        document.getElementById('cmp-product').innerHTML =
            (item.image_url
                ? '<img class="pimg" src="' + item.image_url + '" alt="">'
                : '<div class="pimg">' + esc(item.initials) + '</div>')
            + '<div><div style="font-weight:600;">' + esc(item.name) + '</div>'
            + '<div class="muted">' + esc(item.brand) + ' &middot; ' + esc(item.category) + '</div>'
            + '<div class="mrpline">MRP <b>' + money(item.mrp) + '</b></div></div>';
        document.getElementById('cmp-dup').hidden = !line;
        document.getElementById('cmp-slabs').innerHTML = slabTableHtml(item)
            + (manual ? '<div class="muted" style="margin:6px 0 2px; font-size:12px;">Tip: set standing rates via MANAGE RATES on the product to skip typing this next time.</div>' : '');
        saveBtn.textContent = line ? 'UPDATE CART' : 'ADD TO CART';
        removeBtn.hidden = !line;

        qtyEl.value = work.qty;
        freeEl.value = work.free_qty;
        schemeEl.value = work.scheme_percent;
        rateEl.value = work.rate > 0 ? work.rate : '';
        derivedRow.hidden = false;
        syncDerived(null);

        render();
        modal.hidden = false;
        if (manual && !line) {
            rateEl.focus();
        } else {
            qtyEl.focus();
            qtyEl.select();
        }
    }

    function render() {
        const w = cmp.work;
        const item = cmp.item;

        document.getElementById('cmp-slabs').querySelectorAll('tbody tr').forEach(tr => tr.classList.remove('active'));
        if (!cmp.manual) {
            const slab = resolveSlab(item.slabs, w.qty);
            if (slab) {
                const idx = item.slabs.indexOf(slab);
                const tr = document.getElementById('cmp-slabs').querySelector('tbody tr[data-slab="' + idx + '"]');
                if (tr) tr.classList.add('active');
            }
        }

        let html = '';
        if (w.qty >= 1 && w.rate > 0) {
            html = hintHtml(item, w.qty, w.rate, w.free_qty);
            if (cmp.manual && item.mrp > 0) {
                html += '<div class="landcost">Factor <b>' + (item.mrp / w.rate).toFixed(2) + '</b>'
                    + ' &middot; <b>' + ((1 - w.rate / item.mrp) * 100).toFixed(1) + '%</b> off MRP</div>';
            }
        }
        if (w.qty >= 1) html += stockWarnHtml(item, w.qty, w.free_qty);
        amountEl.innerHTML = html;
        baseNote.textContent = (w.base > 0 && (w.scheme_percent || 0) > 0)
            ? 'List \u20B9' + w.base.toFixed(2) + ' \u2212 ' + w.scheme_percent + '% scheme'
            : '';
        saveBtn.disabled = !(w.qty >= 1 && w.rate > 0);
    }

    function recalc(changed) {
        const w = cmp.work;

        if (cmp.manual) { render(); return; }

        const slab = resolveSlab(cmp.item.slabs, w.qty) || cmp.item.slabs[0];

        if (changed === 'qty') {
            if (!w.manual_scheme) w.scheme_percent = slab.scheme_percent || 0;
            if (!w.manual_free) w.free_qty = suggestedFree(slab, w.qty);
        }

        if (!w.manual_rate) {
            w.rate = Math.round(slab.rate * (1 - (w.scheme_percent || 0) / 100) * 100) / 100;
        }

        const sBase = (w.scheme_percent || 0) / 100;
        w.base = w.manual_rate
            ? (sBase < 1 ? Math.round(w.rate / (1 - sBase) * 100) / 100 : w.rate)
            : slab.rate;

        if (document.activeElement !== schemeEl) schemeEl.value = w.scheme_percent;
        if (document.activeElement !== freeEl) freeEl.value = w.free_qty;
        if (document.activeElement !== rateEl) rateEl.value = w.rate;
        syncDerived(null);
        render();
    }

    function syncDerived(skip) {
        if (!cmp) return;
        const mrp = cmp.item.mrp;
        const b = cmp.work.base || 0;
        if (skip !== 'factor') factorEl.value = (b > 0 && mrp > 0) ? (mrp / b).toFixed(2) : '';
        if (skip !== 'flat') flatEl.value = (b > 0 && mrp > 0) ? ((1 - b / mrp) * 100).toFixed(1) : '';
    }

    function applyBase() {
        const w = cmp.work;
        const s = (w.scheme_percent || 0) / 100;
        w.rate = Math.round(w.base * (1 - s) * 100) / 100;
        w.manual_rate = true;
        rateEl.value = w.rate > 0 ? w.rate : '';
    }

    factorEl.addEventListener('input', () => {
        if (!cmp) return;
        const f = parseFloat(factorEl.value);
        if (!isNaN(f) && f > 0 && cmp.item.mrp > 0) {
            cmp.work.base = Math.round((cmp.item.mrp / f) * 100) / 100;
            applyBase();
        }
        syncDerived('factor');
        render();
    });

    flatEl.addEventListener('input', () => {
        if (!cmp) return;
        const fl = parseFloat(flatEl.value);
        if (!isNaN(fl) && fl < 100 && cmp.item.mrp > 0) {
            cmp.work.base = Math.round((cmp.item.mrp * (1 - fl / 100)) * 100) / 100;
            applyBase();
        }
        syncDerived('flat');
        render();
    });

    qtyEl.addEventListener('input', () => {
        if (!cmp) return;
        cmp.work.qty = Math.max(1, Math.floor(parseInt(qtyEl.value, 10) || 0));
        recalc('qty');
    });

    freeEl.addEventListener('input', () => {
        if (!cmp) return;
        cmp.work.free_qty = Math.max(0, Math.floor(parseInt(freeEl.value, 10) || 0));
        cmp.work.manual_free = true;
        render();
    });

    schemeEl.addEventListener('input', () => {
        if (!cmp) return;
        cmp.work.scheme_percent = Math.min(100, Math.max(0, parseFloat(schemeEl.value) || 0));
        cmp.work.manual_scheme = true;
        if (cmp.manual && cmp.work.base > 0) {
            applyBase();
            render();
            return;
        }
        recalc('scheme');
    });

    rateEl.addEventListener('input', () => {
        if (!cmp) return;
        const r = parseFloat(rateEl.value);
        if (!isNaN(r) && r > 0) {
            cmp.work.rate = Math.round(r * 100) / 100;
            cmp.work.manual_rate = true;
            {
                const s = (cmp.work.scheme_percent || 0) / 100;
                cmp.work.base = s < 1 ? Math.round(cmp.work.rate / (1 - s) * 100) / 100 : cmp.work.rate;
            }
        } else {
            cmp.work.rate = 0;
            cmp.work.base = 0;
        }
        syncDerived(null);
        render();
    });

    [qtyEl, freeEl, schemeEl, rateEl, factorEl, flatEl].forEach(el => {
        el.addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); if (!saveBtn.disabled) saveBtn.click(); }
        });
    });

    saveBtn.addEventListener('click', () => {
        if (!cmp) return;
        const w = cmp.work;
        const item = cmp.item;
        if (w.qty < 1 || w.rate <= 0) return;

        const cart = opts.getCart();
        let line = getLine(item.id);
        if (!line) {
            line = { product_id: item.id, name: item.name, brand: item.brand, category: item.category, mrp: item.mrp, tax_percent: item.tax_percent || 0, tax_inclusive: item.tax_inclusive !== false };
            cart.lines.push(line);
        }
        line.qty = w.qty;
        line.free_qty = w.free_qty;
        line.scheme_percent = w.scheme_percent;
        line.rate = w.rate;
        line.manual_rate = w.manual_rate;
        line.manual_free = w.manual_free;

        opts.saveCart();
        const id = item.id;
        close();
        opts.onChange(id);
    });

    removeBtn.addEventListener('click', () => {
        if (!cmp) return;
        const cart = opts.getCart();
        const id = cmp.item.id;
        cart.lines = cart.lines.filter(l => l.product_id !== id);
        opts.saveCart();
        close();
        opts.onChange(id);
    });

    function close() {
        modal.hidden = true;
        cmp = null;
    }

    document.getElementById('cmp-close').addEventListener('click', close);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && cmp) close();
    });

    return { open };
}

// ---------------------------------------------------------------
// Product search factory: suggestions + barcode-wedge + query.
// opts: { input, clearBtn, sugEl, dataUrl, onPick(item), onQuery(q) }
// ---------------------------------------------------------------
export function createProductSearch(opts) {
    const { input, clearBtn, sugEl, dataUrl } = opts;
    const nav = window.UI.listKeyNav(input, sugEl, '.sug-item');
    const byId = {};
    let timer = null;

    function resetBox() {
        input.value = '';
        clearBtn.hidden = true;
        sugEl.hidden = true;
        nav.reset();
    }

    input.addEventListener('input', () => {
        clearTimeout(timer);
        clearBtn.hidden = input.value === '';
        const term = input.value.trim();
        if (!term) {
            sugEl.hidden = true;
            nav.reset();
            opts.onQuery('');
            return;
        }
        timer = setTimeout(async () => {
            const res = await fetch(dataUrl + '?' + new URLSearchParams({ q: term, limit: 6, with_slabs: '1' }));
            const data = await res.json();
            if (!data.items.length) { sugEl.hidden = true; nav.reset(); return; }
            data.items.forEach(i => { byId[i.id] = i; });
            sugEl.innerHTML = data.items.map(i =>
                '<div class="sug-item" data-id="' + i.id + '">'
                + (i.image_url
                    ? '<img class="sug-img" src="' + i.image_url + '" alt="">'
                    : '<div class="sug-img">' + esc(i.initials) + '</div>')
                + '<div><div>' + esc(i.name) + '</div>'
                + '<div class="muted">MRP ' + money(i.mrp) + '</div></div>'
                + '</div>'
            ).join('');
            sugEl.hidden = false;
            nav.reset();
        }, 250);
    });

    function pick(el) {
        const item = byId[parseInt(el.dataset.id, 10)];
        resetBox();
        if (item) opts.onPick(item);
    }

    async function tryBarcode(code) {
        const res = await fetch(dataUrl + '?' + new URLSearchParams({ q: code, limit: 3, with_slabs: '1' }));
        const data = await res.json();
        const hit = data.items.find(i => i.barcode === code);
        if (hit) {
            byId[hit.id] = hit;
            resetBox();
            opts.onPick(hit);
            return true;
        }
        return false;
    }

    input.addEventListener('keydown', async e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const active = nav.active();
            if (active) { pick(active); return; }

            const term = input.value.trim();
            if (/^\d{8,14}$/.test(term)) {
                const found = await tryBarcode(term);
                if (found) return;
            }

            sugEl.hidden = true;
            opts.onQuery(term);
        }
        if (e.key === 'Escape') sugEl.hidden = true;
    });

    sugEl.addEventListener('click', e => {
        const item = e.target.closest('.sug-item');
        if (item) pick(item);
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('.searchwrap')) sugEl.hidden = true;
    });

    clearBtn.addEventListener('click', () => {
        resetBox();
        opts.onQuery('');
        input.focus();
    });

    return { resetBox };
}

// ---------------------------------------------------------------
// JSON fetch with CSRF for POST/DELETE.
// ---------------------------------------------------------------
export async function api(url, method = 'GET', body) {
    const res = await fetch(url, {
        method,
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            ...(body ? { 'Content-Type': 'application/json' } : {}),
        },
        body: body ? JSON.stringify(body) : undefined,
    });
    if (!res.ok) {
        let msg = 'Request failed: ' + res.status;
        try { const d = await res.json(); if (d && d.message) msg = d.message; } catch (e) {}
        throw new Error(msg);
    }
    return res.json();
}

// ---------------------------------------------------------------
// Held-bills modal factory. Requires the _held partial in the DOM.
// opts: { holdUrl, getCart, onResume(cart) }
//   getCart may return null/empty cart; a cart with lines triggers
//   the HOLD & RESUME / DISCARD & RESUME choice before resuming.
// Returns { open }.
// ---------------------------------------------------------------
export function createHeldBills(opts) {
    const modal = document.getElementById('heldmodal');
    const body = document.getElementById('held-body');

    async function open() {
        modal.hidden = false;
        body.innerHTML = '<p class="muted">Loading&hellip;</p>';
        await renderList();
    }

    async function renderList() {
        const data = await api(opts.holdUrl);
        if (!data.items.length) {
            body.innerHTML = '<p class="muted">No held bills.</p>';
            return;
        }
        body.innerHTML = data.items.map(h =>
            '<div class="card held-item" data-id="' + h.id + '" data-firm="' + esc(h.firm_name) + '">'
            + '<div style="font-weight:600;">' + esc(h.firm_name) + '</div>'
            + '<div class="muted">' + esc(h.held_at) + ' &middot; ' + h.line_count + ' item' + (h.line_count === 1 ? '' : 's')
            + ' &middot; ' + money(h.total) + '</div>'
            + '<div class="heldbtns">'
            + '<button type="button" class="btn h-resume">RESUME</button>'
            + '<button type="button" class="btn btn-outline h-discard">DISCARD</button>'
            + '</div></div>'
        ).join('');
    }

    function renderChoice(id, firm) {
        const cart = opts.getCart();
        body.innerHTML =
            '<div class="card">'
            + '<div style="font-weight:600; margin-bottom:4px;">Current cart has ' + cart.lines.length + ' item(s) for ' + esc(cart.partner.firm_name) + '.</div>'
            + '<div class="muted">What should happen to it before resuming the bill for ' + esc(firm) + '?</div>'
            + '</div>'
            + '<button type="button" class="btn h-choice" data-mode="hold" data-id="' + id + '">HOLD CURRENT &amp; RESUME</button>'
            + '<div style="margin-top:8px;"><button type="button" class="btn btn-outline h-choice" data-mode="discard" data-id="' + id + '">DISCARD CURRENT &amp; RESUME</button></div>'
            + '<div style="margin-top:8px;"><button type="button" class="btn btn-outline h-back">CANCEL</button></div>';
    }

    async function doResume(id, mode) {
        const cart = opts.getCart();
        if (mode === 'hold' && cart && cart.lines && cart.lines.length) {
            await api(opts.holdUrl, 'POST', {
                partner_id: cart.partner.id,
                payload: { lines: cart.lines, discount: cart.discount || null },
            });
        }
        const h = await api(opts.holdUrl + '/' + id);
        await api(opts.holdUrl + '/' + id, 'DELETE');
        const resumed = {
            partner: h.partner,
            lines: h.payload.lines || [],
            discount: h.payload.discount || null,
        };
        modal.hidden = true;
        opts.onResume(resumed);
    }

    body.addEventListener('click', async e => {
        const choice = e.target.closest('.h-choice');
        if (choice) {
            await doResume(parseInt(choice.dataset.id, 10), choice.dataset.mode);
            return;
        }
        if (e.target.closest('.h-back')) {
            await renderList();
            return;
        }

        const item = e.target.closest('.held-item');
        if (!item) return;
        const id = parseInt(item.dataset.id, 10);
        const firm = item.dataset.firm;

        if (e.target.closest('.h-resume')) {
            const cart = opts.getCart();
            if (cart && cart.lines && cart.lines.length) {
                renderChoice(id, firm);
            } else {
                await doResume(id, 'discard');
            }
        }

        if (e.target.closest('.h-discard')) {
            if (confirm('Discard the held bill for ' + firm + '? This cannot be undone.')) {
                await api(opts.holdUrl + '/' + id, 'DELETE');
                await renderList();
            }
        }
    });

    document.getElementById('held-close').addEventListener('click', () => { modal.hidden = true; });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !modal.hidden) modal.hidden = true;
    });

    return { open };
}
// ---------------------------------------------------------------
// Notice modal: B.notify(title, message). Builds its DOM once.
// ---------------------------------------------------------------
export function notify(title, message) {
    let m = document.getElementById('noticemodal');
    if (!m) {
        m = document.createElement('div');
        m.id = 'noticemodal';
        m.className = 'modal';
        m.innerHTML = '<div class="modal-box">'
            + '<div class="modal-head"><span id="notice-title"></span>'
            + '<button type="button" class="xbtn" id="notice-close">&times;</button></div>'
            + '<div class="cmp-body">'
            + '<div id="notice-msg" style="font-size:14.5px; font-weight:600; color:#1a1a1a; padding:10px 12px; border:1px solid #999; border-left:4px solid #b00020; background:#fdf3f3; border-radius:4px; line-height:1.5;"></div>'
            + '<div style="margin-top:14px;"><button type="button" class="btn" id="notice-ok">OK</button></div>'
            + '</div></div>';
        document.body.appendChild(m);
        const close = () => { m.hidden = true; };
        m.querySelector('#notice-close').addEventListener('click', close);
        m.querySelector('#notice-ok').addEventListener('click', close);
        m.addEventListener('click', e => { if (e.target === m) close(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && !m.hidden) close(); });
    }
    m.querySelector('#notice-title').textContent = title;
    m.querySelector('#notice-msg').textContent = message;
    m.hidden = false;
}