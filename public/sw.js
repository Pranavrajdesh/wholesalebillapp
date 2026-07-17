// wholesaleBillApp service worker
// Strategy: server is the source of truth. Pages are network-first
// (fresh data always wins); the offline page appears only when the
// network is truly unreachable. No offline billing by design.

const CACHE = 'wba-v1';
const PRECACHE = [
    '/offline',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE).then((cache) => cache.addAll(PRECACHE)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const req = event.request;

    // Same-origin GET only; Vite dev server and external assets pass through.
    if (req.method !== 'GET' || new URL(req.url).origin !== self.location.origin) return;

    // Page navigations: network first, offline fallback.
    if (req.mode === 'navigate') {
        event.respondWith(
            fetch(req).catch(() => caches.match('/offline'))
        );
        return;
    }

    // Icons & static files under /icons or the touch icon: cache first.
    const path = new URL(req.url).pathname;
    if (path.startsWith('/icons/') || path === '/apple-touch-icon.png') {
        event.respondWith(
            caches.match(req).then((hit) => hit || fetch(req).then((res) => {
                const copy = res.clone();
                caches.open(CACHE).then((c) => c.put(req, copy));
                return res;
            }))
        );
    }
});
