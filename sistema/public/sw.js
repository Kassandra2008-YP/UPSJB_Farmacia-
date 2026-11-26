const CACHE_NAME = 'farm-sjb-static-v1';
const STATIC_ASSETS = [
  '/css/bootstrap.css',
  '/css/custom.css',
  '/js/bootstrap.bundle.min.js',
  '/js/app.js',
  '/manifest.webmanifest',
  '/icons/icon-192.svg',
  '/icons/icon-512.svg'
];

self.addEventListener('install', event => {
  event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS)));
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key)))));
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);
  if (url.origin !== self.location.origin) return;

  // Nunca cachear páginas dinámicas ni respuestas con ventas, usuarios o clientes.
  if (!STATIC_ASSETS.includes(url.pathname) && !url.pathname.endsWith('.css') && !url.pathname.endsWith('.js') && !url.pathname.endsWith('.svg')) return;

  event.respondWith(caches.match(event.request).then(cached => cached || fetch(event.request).then(response => {
    if (response.ok) {
      const copy = response.clone();
      caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
    }
    return response;
  })));
});
