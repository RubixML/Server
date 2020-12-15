const ASSETS = [
    '/',
    '/ui/dashboard',
    '/ui/visualizer/scatterplot-2d',
    '/manifest.json',
    '/app.js',
    '/app.css',
    '/images/app-icon-small.png',
    '/images/app-icon-apple-touch.png',
    '/images/app-icon-medium.png',
    '/images/app-icon-large.png',
    '/fonts/fa-solid-900.woff',
    '/fonts/fa-solid-900.woff2',
    '/sounds/sharp.ogg',
];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open('precache').then((cache) => {
        return cache.addAll(ASSETS);
    }));
});

self.addEventListener('fetch', (event) => {
    event.respondWith(caches.match(event.request).then((response) => {
        return response || fetch(event.request);
    }));
});
