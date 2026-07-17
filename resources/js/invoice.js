import QRCode from 'qrcode';

window.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('upiqr');
    if (!el || !el.dataset.upi) return;
    QRCode.toCanvas(el.dataset.upi, { width: 140, margin: 1, color: { dark: '#111111', light: '#ffffff' } }, (err, canvas) => {
        if (!err && canvas) el.appendChild(canvas);
    });
});