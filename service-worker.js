// Basic passthrough service worker for PWA installability.
const CACHE_NAME = 'sanctuary-shell-v1';

self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request)).then((resp) => {
      if (resp) return resp;
      return new Response('Offline', { status: 503, statusText: 'Offline' });
    })
  );
});
