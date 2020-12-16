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
    '/fonts/roboto-regular.woff2',
    '/fonts/roboto-regular.woff',
    '/fonts/roboto-500.woff2',
    '/fonts/roboto-500.woff',
    '/fonts/fa-solid-900.woff2',
    '/fonts/fa-solid-900.woff',
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
