const CACHE_NAME = 'azmoon-cache-v2';
const OFFLINE_URL = '/offline.html';

// Only cache static assets, NOT HTML pages
const STATIC_ASSETS = [
    '/offline.html',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        (async () => {
            // Clear old caches
            const keys = await caches.keys();
            await Promise.all(keys.map((k) => (k === CACHE_NAME ? null : caches.delete(k))));
            await self.clients.claim();
        })()
    );
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    const url = new URL(req.url);

    // Only handle GET requests
    if (req.method !== 'GET') {
        return;
    }

    // For HTML navigations: ALWAYS use network-first (no caching)
    if (req.mode === 'navigate' || req.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(req).catch(async () => {
                // Only show offline page if network fails
                const cache = await caches.open(CACHE_NAME);
                const cached = await cache.match(OFFLINE_URL);
                return cached || new Response('Offline', { status: 503, statusText: 'Service Unavailable' });
            })
        );
        return;
    }

    // For static assets (CSS, JS, images): cache-first
    if (url.origin === location.origin && (
        req.url.includes('/css/') || 
        req.url.includes('/js/') || 
        req.url.includes('/images/') ||
        req.url.includes('/build/')
    )) {
        event.respondWith(
            caches.match(req).then((cached) => {
                return cached || fetch(req).then((res) => {
                    if (res && res.status === 200 && res.type === 'basic') {
                        const resClone = res.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(req, resClone));
                    }
                    return res;
                });
            })
        );
        return;
    }

    // For everything else: network-only (no caching)
    event.respondWith(fetch(req));
});
