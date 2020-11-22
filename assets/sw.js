self.addEventListener('install', function(event) {
    event.waitUntil(caches.open('precache').then(function(cache) {
        return cache.addAll([
            '/',
            '/server',
            '/app.js',
            '/app.css',
            '/images/app-icon-large.png',
            '/images/app-icon-small.png',
            '/fonts/fa-solid-900.woff',
            '/fonts/fa-solid-900.woff2',
            '/sounds/sharp.ogg',
            '/manifest.json',
        ]);
    }));
});

self.addEventListener('fetch', function(event) {
    event.respondWith(caches.match(event.request).then(function(response) {
        return response || fetch(event.request);
    }));
});