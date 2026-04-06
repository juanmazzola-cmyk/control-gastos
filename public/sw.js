const CACHE_NAME = 'gastos-v1';

self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', () => self.clients.claim());

self.addEventListener('fetch', (event) => {
    // Solo cachear GET, dejar pasar el resto (Livewire POST, etc.)
    if (event.request.method !== 'GET') return;
    // No cachear rutas de Livewire
    if (event.request.url.includes('/livewire/')) return;

    event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
    );
});
