self.addEventListener('install', function(event) {
    event.waitUntil(caches.open('precache').then(function(cache) {
        return cache.addAll([
            '/',
            '/server',
            '/manifest.json',
            '/app.js',
            '/app.css',
            '/images/app-icon-small.png',
            '/images/app-icon-large.png',
            '/fonts/fa-solid-900.woff',
            '/fonts/fa-solid-900.woff2',
            '/sounds/sharp.ogg',
        ]);
    }));
});

self.addEventListener('fetch', function(event) {
    event.respondWith(caches.match(event.request).then(function(response) {
        return response || fetch(event.request);
    }));
});
