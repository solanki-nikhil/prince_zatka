// Service Worker for Push Notifications
const CACHE_NAME = 'prince-zatka-v1';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js'
];

// Install event
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache);
            })
    );
});

// Fetch event
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
    );
});

// Push event - Handle incoming push notifications
self.addEventListener('push', event => {
    console.log('Push event received:', event);
    
    let notificationData = {
        title: 'Order Status Updated',
        body: 'Your order status has been updated',
        icon: '/images/logo.png',
        badge: '/images/badge.png',
        data: {
            url: '/customer/orders'
        }
    };

    // Parse the push message data if available
    if (event.data) {
        try {
            const data = event.data.json();
            notificationData = {
                title: data.title || notificationData.title,
                body: data.body || notificationData.body,
                icon: data.icon || notificationData.icon,
                badge: data.badge || notificationData.badge,
                data: {
                    ...notificationData.data,
                    ...data.data
                },
                actions: data.actions || []
            };
        } catch (e) {
            console.log('Error parsing push data:', e);
        }
    }

    const options = {
        body: notificationData.body,
        icon: notificationData.icon,
        badge: notificationData.badge,
        data: notificationData.data,
        actions: notificationData.actions,
        requireInteraction: true,
        vibrate: [200, 100, 200],
        tag: 'order-status-update'
    };

    event.waitUntil(
        self.registration.showNotification(notificationData.title, options)
    );
});

// Notification click event
self.addEventListener('notificationclick', event => {
    console.log('Notification clicked:', event);
    
    event.notification.close();

    if (event.action === 'view') {
        // Handle "View Order" action
        event.waitUntil(
            clients.openWindow(event.notification.data.url || '/customer/orders')
        );
    } else {
        // Default action - open the notification URL
        event.waitUntil(
            clients.openWindow(event.notification.data.url || '/customer/orders')
        );
    }
});

// Notification close event
self.addEventListener('notificationclose', event => {
    console.log('Notification closed:', event);
});

// Background sync event (for offline functionality)
self.addEventListener('sync', event => {
    console.log('Background sync event:', event);
    
    if (event.tag === 'background-sync') {
        event.waitUntil(
            // Handle background sync tasks
            console.log('Background sync completed')
        );
    }
});

// Message event - Handle messages from the main thread
self.addEventListener('message', event => {
    console.log('Message received in service worker:', event);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
}); 