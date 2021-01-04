import { precacheAndRoute, createHandlerBoundToURL, cleanupOutdatedCaches } from 'workbox-precaching';
import { registerRoute, NavigationRoute } from 'workbox-routing';

/**
 * Precache static assets.
 */

const precacheManifest = [
    { url: '/ui', revision: '0' },
    { url: '/manifest.json', revision: '0' },
    { url: '/app.js', revision: '0' },
    { url: '/app.css', revision: '0' },
    { url: '/images/app-icon-small.png', revision: '0' },
    { url: '/images/app-icon-apple-touch.png', revision: '0' },
    { url: '/images/app-icon-medium.png', revision: '0' },
    { url: '/images/app-icon-large.png', revision: '0' },
    { url: '/fonts/Roboto-300.woff2', revision: '0' },
    { url: '/fonts/Roboto-300.woff', revision: '0' },
    { url: '/fonts/Roboto-regular.woff2', revision: '0' },
    { url: '/fonts/Roboto-regular.woff', revision: '0' },
    { url: '/fonts/Roboto-500.woff2', revision: '0' },
    { url: '/fonts/Roboto-500.woff', revision: '0' },
    { url: '/fonts/fa-solid-900.woff2', revision: '0' },
    { url: '/fonts/fa-solid-900.woff', revision: '0' },
    { url: '/sounds/sharp.ogg', revision: '0' },
];

precacheAndRoute(precacheManifest);

/**
 * Serve the app shell for navigation requests.
 */

const handler = createHandlerBoundToURL('/ui');

const navigationRoute = new NavigationRoute(handler, {
    allowlist: [
        new RegExp('/ui/'),
      ],
});

registerRoute(navigationRoute);

/**
 * Listen for skip waiting signal from main thread.
 */

addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        skipWaiting();
    }
});

/**
 * Clean up caches used by previous versions.
 */

cleanupOutdatedCaches();
