<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use App\Models\Order;
use App\Notifications\OrderStatusUpdateNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Send push notification to user for order status update
     */
    public function sendOrderStatusNotification(Order $order, string $statusText): void
    {
        try {
            $user = $order->user;
            
            if (!$user) {
                Log::warning("No user found for order: {$order->id}");
                return;
            }

            // Send Laravel notification (database + broadcast)
            $user->notify(new OrderStatusUpdateNotification($order, $statusText));

            // Send web push notification
            $this->sendWebPushNotification($user, $order, $statusText);

        } catch (\Exception $e) {
            Log::error("Failed to send order status notification: " . $e->getMessage(), [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'status' => $statusText
            ]);
        }
    }

    /**
     * Send web push notification using service worker
     */
    private function sendWebPushNotification(User $user, Order $order, string $statusText): void
    {
        $subscriptions = PushSubscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        foreach ($subscriptions as $subscription) {
            try {
                $this->sendPushToSubscription($subscription, $order, $statusText);
            } catch (\Exception $e) {
                Log::error("Failed to send push to subscription: " . $e->getMessage(), [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id
                ]);
                
                // Mark subscription as inactive if it fails
                $subscription->update(['is_active' => false]);
            }
        }
    }

    /**
     * Send push notification to a specific subscription
     */
    private function sendPushToSubscription(PushSubscription $subscription, Order $order, string $statusText): void
    {
        $payload = [
            'title' => 'Order Status Updated',
            'body' => "Your order {$order->order_id} status has been updated to {$statusText}",
            'icon' => asset('images/logo.png'),
            'badge' => asset('images/badge.png'),
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_id,
                'status' => $statusText,
                'url' => route('customer.orders'),
                'type' => 'order_status_update'
            ],
            'actions' => [
                [
                    'action' => 'view',
                    'title' => 'View Order',
                    'icon' => asset('images/view-icon.png')
                ]
            ]
        ];

        // For now, we'll use a simple HTTP request to the push service
        // In production, you might want to use a proper push service library
        $response = Http::post($subscription->endpoint, [
            'subscription' => [
                'endpoint' => $subscription->endpoint,
                'keys' => [
                    'p256dh' => $subscription->p256dh_key,
                    'auth' => $subscription->auth_token
                ]
            ],
            'payload' => $payload
        ]);

        if (!$response->successful()) {
            throw new \Exception("Push notification failed: " . $response->body());
        }
    }

    /**
     * Store push subscription for a user
     */
    public function storeSubscription(User $user, array $subscriptionData): PushSubscription
    {
        return PushSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'endpoint' => $subscriptionData['endpoint']
            ],
            [
                'p256dh_key' => $subscriptionData['keys']['p256dh'],
                'auth_token' => $subscriptionData['keys']['auth'],
                'device_type' => $subscriptionData['device_type'] ?? 'web',
                'is_active' => true
            ]
        );
    }

    /**
     * Remove push subscription for a user
     */
    public function removeSubscription(User $user, string $endpoint): bool
    {
        return PushSubscription::where('user_id', $user->id)
            ->where('endpoint', $endpoint)
            ->delete() > 0;
    }
} 