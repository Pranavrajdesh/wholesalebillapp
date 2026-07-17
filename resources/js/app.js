import { comboSelect, initGoTop, listKeyNav } from './ui';
import * as Billing from './billing';

window.UI = { comboSelect, initGoTop, listKeyNav };
window.Billing = Billing;

// Global copy-to-clipboard for .copybtn inside .inputrow
document.addEventListener('click', e => {
    const b = e.target.closest('.copybtn');
    if (!b) return;
    const inp = b.closest('.inputrow')?.querySelector('input');
    if (!inp || !inp.value) return;

    const done = () => {
        const old = b.innerHTML;
        b.innerHTML = '&#10003;';
        setTimeout(() => { b.innerHTML = old; }, 900);
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(inp.value).then(done);
    } else {
        inp.select();
        document.execCommand('copy');
        window.getSelection().removeAllRanges();
        done();
    }
});