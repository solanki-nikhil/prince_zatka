// Push Notification Management
class PushNotificationManager {
    constructor() {
        this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
        this.registration = null;
        this.subscription = null;
    }

    // Initialize push notifications
    async init() {
        if (!this.isSupported) {
            console.log('Push notifications are not supported');
            return false;
        }

        try {
            // Register service worker
            this.registration = await navigator.serviceWorker.register('/sw.js');
            console.log('Service Worker registered:', this.registration);

            // Check if user is already subscribed
            this.subscription = await this.registration.pushManager.getSubscription();
            
            if (this.subscription) {
                console.log('User is already subscribed to push notifications');
                return true;
            }

            return true;
        } catch (error) {
            console.error('Error initializing push notifications:', error);
            return false;
        }
    }

    // Request notification permission
    async requestPermission() {
        if (!this.isSupported) {
            return false;
        }

        try {
            const permission = await Notification.requestPermission();
            return permission === 'granted';
        } catch (error) {
            console.error('Error requesting notification permission:', error);
            return false;
        }
    }

    // Subscribe to push notifications
    async subscribe() {
        if (!this.isSupported || !this.registration) {
            console.log('Push notifications not supported or not initialized');
            return false;
        }

        try {
            // Request permission first
            const permissionGranted = await this.requestPermission();
            if (!permissionGranted) {
                console.log('Notification permission denied');
                return false;
            }

            // Get VAPID public key from server
            const response = await fetch('/api/push/vapid-public-key');
            const { publicKey } = await response.json();

            // Convert VAPID key to Uint8Array
            const vapidPublicKey = this.urlBase64ToUint8Array(publicKey);

            // Subscribe to push notifications
            this.subscription = await this.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: vapidPublicKey
            });

            console.log('Push subscription created:', this.subscription);

            // Send subscription to server
            await this.sendSubscriptionToServer(this.subscription);

            return true;
        } catch (error) {
            console.error('Error subscribing to push notifications:', error);
            return false;
        }
    }

    // Unsubscribe from push notifications
    async unsubscribe() {
        if (!this.subscription) {
            console.log('No active subscription to unsubscribe from');
            return false;
        }

        try {
            await this.subscription.unsubscribe();
            console.log('Push subscription unsubscribed');

            // Remove subscription from server
            await this.removeSubscriptionFromServer(this.subscription.endpoint);

            this.subscription = null;
            return true;
        } catch (error) {
            console.error('Error unsubscribing from push notifications:', error);
            return false;
        }
    }

    // Send subscription to server
    async sendSubscriptionToServer(subscription) {
        try {
            const response = await fetch('/api/push/subscriptions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    keys: {
                        p256dh: this.arrayBufferToBase64(subscription.getKey('p256dh')),
                        auth: this.arrayBufferToBase64(subscription.getKey('auth'))
                    },
                    device_type: 'web'
                })
            });

            const result = await response.json();
            
            if (result.status) {
                console.log('Subscription saved to server');
                this.showNotification('Push notifications enabled', 'You will now receive notifications for order updates.');
            } else {
                console.error('Failed to save subscription:', result.message);
            }
        } catch (error) {
            console.error('Error sending subscription to server:', error);
        }
    }

    // Remove subscription from server
    async removeSubscriptionFromServer(endpoint) {
        try {
            const response = await fetch('/api/push/subscriptions', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    endpoint: endpoint
                })
            });

            const result = await response.json();
            
            if (result.status) {
                console.log('Subscription removed from server');
                this.showNotification('Push notifications disabled', 'You will no longer receive notifications.');
            } else {
                console.error('Failed to remove subscription:', result.message);
            }
        } catch (error) {
            console.error('Error removing subscription from server:', error);
        }
    }

    // Show a notification (for testing)
    showNotification(title, body) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                body: body,
                icon: '/images/logo.png'
            });
        }
    }

    // Utility function to convert VAPID key
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    // Utility function to convert ArrayBuffer to Base64
    arrayBufferToBase64(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return window.btoa(binary);
    }

    // Get subscription status
    getSubscriptionStatus() {
        return {
            isSupported: this.isSupported,
            isSubscribed: !!this.subscription,
            permission: Notification.permission
        };
    }
}

// Initialize push notification manager when DOM is loaded
document.addEventListener('DOMContentLoaded', async () => {
    window.pushNotificationManager = new PushNotificationManager();
    
    // Initialize push notifications
    const initialized = await window.pushNotificationManager.init();
    
    if (initialized) {
        console.log('Push notification manager initialized');
        
        // Add notification toggle button if user is logged in
        if (document.querySelector('meta[name="user-id"]')) {
            addNotificationToggleButton();
        }
    }
});

// Add notification toggle button to the page
function addNotificationToggleButton() {
    const status = window.pushNotificationManager.getSubscriptionStatus();
    
    // Create toggle button
    const toggleButton = document.createElement('button');
    toggleButton.id = 'notification-toggle';
    toggleButton.className = 'btn btn-sm ' + (status.isSubscribed ? 'btn-success' : 'btn-outline-secondary');
    toggleButton.innerHTML = `
        <i class="fas fa-bell"></i>
        ${status.isSubscribed ? 'Notifications ON' : 'Notifications OFF'}
    `;
    
    // Add click handler
    toggleButton.addEventListener('click', async () => {
        if (status.isSubscribed) {
            await window.pushNotificationManager.unsubscribe();
            toggleButton.className = 'btn btn-sm btn-outline-secondary';
            toggleButton.innerHTML = '<i class="fas fa-bell"></i> Notifications OFF';
        } else {
            await window.pushNotificationManager.subscribe();
            toggleButton.className = 'btn btn-sm btn-success';
            toggleButton.innerHTML = '<i class="fas fa-bell"></i> Notifications ON';
        }
    });
    
    // Add to page (you can customize where to place this)
    const header = document.querySelector('.navbar-nav');
    if (header) {
        const li = document.createElement('li');
        li.className = 'nav-item';
        li.appendChild(toggleButton);
        header.appendChild(li);
    }
} 