const CACHE_PREFIX = 'hypesole-pwa-';
const CACHE_NAME = `${CACHE_PREFIX}v4`;
const OWNED_CACHE_PREFIXES = [CACHE_PREFIX, 'hypesole-shell-', 'shoestentic-shell-'];
const scopeUrl = new URL(self.registration.scope);
const offlineUrl = new URL('offline.html', scopeUrl).href;
const manifestUrl = new URL('manifest.webmanifest', scopeUrl).href;
const coreAssets = [
  offlineUrl,
  manifestUrl,
  new URL('icons/icon-192.png', scopeUrl).href,
  new URL('icons/icon-512.png', scopeUrl).href,
  new URL('icons/icon-512-maskable.png', scopeUrl).href,
];
const privatePath = /^(admin|customer|courier|checkout|keranjang|pesanan|orders|order|login|logout|otp|api|storage|photos|payment|transaksi|shipments)(\/|$)/i;

self.addEventListener('install', (event) => {
  event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (event) => {
  event.waitUntil((async () => {
    const keys = await caches.keys();
    await Promise.all(keys
      .filter((key) => key !== CACHE_NAME && OWNED_CACHE_PREFIXES.some((prefix) => key.toLowerCase().startsWith(prefix)))
      .map((key) => caches.delete(key)));

    const cache = await caches.open(CACHE_NAME);
    for (const asset of coreAssets) {
      try {
        const response = await fetch(asset, { cache: 'reload' });
        if (response.ok) await cache.put(asset, response);
      } catch {
        // Activation should still succeed; navigation fallback remains safe if offline.
      }
    }

    await self.clients.claim();
  })());
});

self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  if (request.method !== 'GET' || url.origin !== self.location.origin || !url.pathname.startsWith(scopeUrl.pathname)) return;

  const relativePath = url.pathname.slice(scopeUrl.pathname.length).replace(/^\/+/, '');
  if (privatePath.test(relativePath)) return;

  if (request.mode === 'navigate') {
    event.respondWith((async () => {
      try {
        return await fetch(request);
      } catch {
        return (await caches.match(offlineUrl)) || Response.error();
      }
    })());
    return;
  }

  const isStaticAsset = /^(build\/.*\.(css|js)|icons\/.*\.png|manifest\.webmanifest)$/i.test(relativePath);
  if (!isStaticAsset) return;

  event.respondWith((async () => {
    const cache = await caches.open(CACHE_NAME);
    const cached = await cache.match(request);
    if (cached) return cached;

    try {
      const response = await fetch(request);
      if (response.ok && response.type === 'basic') await cache.put(request, response.clone());
      return response;
    } catch {
      return new Response('Asset tidak tersedia saat offline.', {
        status: 504,
        statusText: 'Gateway Timeout',
        headers: { 'Content-Type': 'text/plain; charset=utf-8' },
      });
    }
  })());
});
