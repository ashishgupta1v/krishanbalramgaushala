/* ════════════════════════════════════════════════════════════
   GauSeva Connect — Service Worker
   Robust offline-first strategy with safe cache.addAll()
   ════════════════════════════════════════════════════════════ */

const CACHE_NAME   = 'gauseva-v3';
const CACHE_STATIC = 'gauseva-static-v3';

// Critical shell — ONLY files we know exist
const SHELL_URLS = [
  '/',
  '/manifest.json',
  '/favicon.ico',
  '/logo.webp',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
];

// ── INSTALL: pre-cache shell with individual try-catch ──────
self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then(async (cache) => {
      // Cache each URL individually so one failure doesn't break all
      const results = await Promise.allSettled(
        SHELL_URLS.map(url =>
          cache.add(url).catch(err => {
            console.warn('[SW] Failed to cache', url, err.message);
          })
        )
      );
      const ok = results.filter(r => r.status === 'fulfilled').length;
      console.log(`[SW] Installed. Cached ${ok}/${SHELL_URLS.length} shell assets.`);
    })
  );
});

// ── ACTIVATE: remove old caches ─────────────────────────────
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys
          .filter(k => k !== CACHE_NAME && k !== CACHE_STATIC)
          .map(k => caches.delete(k))
      )
    ).then(() => self.clients.claim())
  );
});

// ── FETCH: network-first for pages, cache-first for assets ──
self.addEventListener('fetch', (event) => {
  const req = event.request;
  const url = new URL(req.url);

  // Skip non-GET and cross-origin (except Google Fonts)
  if (req.method !== 'GET') return;
  const isOwnOrigin = url.origin === self.location.origin;
  const isGoogleFont = url.hostname.includes('fonts.gstatic.com') ||
                       url.hostname.includes('fonts.googleapis.com');
  if (!isOwnOrigin && !isGoogleFont) return;

  // 1. Vite build assets (hashed filenames) → Cache-First, long TTL
  if (url.pathname.startsWith('/build/assets/')) {
    event.respondWith(
      caches.open(CACHE_STATIC).then(async (cache) => {
        const cached = await cache.match(req);
        if (cached) return cached;
        const res = await fetch(req);
        if (res.ok) cache.put(req, res.clone());
        return res;
      })
    );
    return;
  }

  // 2. Static images / fonts → Stale-While-Revalidate
  if (
    isGoogleFont ||
    /\.(png|jpg|jpeg|webp|gif|ico|svg|woff2?|ttf|eot)$/.test(url.pathname)
  ) {
    event.respondWith(
      caches.open(CACHE_NAME).then(async (cache) => {
        const cached = await cache.match(req);
        const fetchPromise = fetch(req).then(res => {
          if (res.ok) cache.put(req, res.clone());
          return res;
        }).catch(() => cached);
        return cached || fetchPromise;
      })
    );
    return;
  }

  // 3. HTML pages → Network-First, fall back to cache or /
  if (req.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      fetch(req)
        .then(res => {
          if (res.ok) {
            const clone = res.clone();
            caches.open(CACHE_NAME).then(c => c.put(req, clone));
          }
          return res;
        })
        .catch(async () => {
          const cached = await caches.match(req);
          return cached || caches.match('/') || new Response('Offline', { status: 503 });
        })
    );
    return;
  }

  // 4. Everything else (API, JSON, etc.) → Network only, no caching
  // Do nothing — let the browser handle it normally
});
