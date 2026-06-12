// Service Worker — Aplikasi PWA Masjid
// Strategi sesuai System Design:
//  - Aset statis: Cache First
//  - Halaman HTML: Network First (fallback cache / offline)
//  - API jadwal sholat: Stale While Revalidate
const VERSION = 'masjid-v1';
const STATIC_CACHE = `${VERSION}-static`;
const PAGE_CACHE = `${VERSION}-pages`;
const API_CACHE = `${VERSION}-api`;

const PRECACHE = ['/', '/offline.html', '/manifest.webmanifest', '/icons/icon.svg'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => cache.addAll(PRECACHE)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => !k.startsWith(VERSION)).map((k) => caches.delete(k)))
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);

    // API jadwal sholat — Stale While Revalidate
    if (url.pathname.startsWith('/api/v1/prayer-times')) {
        event.respondWith(staleWhileRevalidate(request, API_CACHE));
        return;
    }

    // Aset statis (build Vite, ikon, gambar) — Cache First
    if (/\.(?:css|js|svg|png|jpg|jpeg|webp|woff2?)$/.test(url.pathname) || url.pathname.startsWith('/build/')) {
        event.respondWith(cacheFirst(request, STATIC_CACHE));
        return;
    }

    // Navigasi halaman — Network First, fallback offline
    if (request.mode === 'navigate') {
        event.respondWith(networkFirst(request));
        return;
    }
});

async function cacheFirst(request, cacheName) {
    const cached = await caches.match(request);
    if (cached) return cached;
    const resp = await fetch(request);
    const cache = await caches.open(cacheName);
    cache.put(request, resp.clone());
    return resp;
}

async function networkFirst(request) {
    try {
        const resp = await fetch(request);
        const cache = await caches.open(PAGE_CACHE);
        cache.put(request, resp.clone());
        return resp;
    } catch (e) {
        const cached = await caches.match(request);
        return cached || caches.match('/offline.html');
    }
}

async function staleWhileRevalidate(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);
    const network = fetch(request).then((resp) => {
        cache.put(request, resp.clone());
        return resp;
    }).catch(() => cached);
    return cached || network;
}
