<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Order;

class OrderStatusUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $statusText;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $statusText)
    {
        $this->order = $order;
        $this->statusText = $statusText;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Status Updated')
            ->line('Your order status has been updated.')
            ->line('Order ID: ' . $this->order->order_id)
            ->line('New Status: ' . $this->statusText)
            ->action('View Order', url('/customer/orders'))
            ->line('Thank you for using our service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_id,
            'status' => $this->statusText,
            'message' => "Your order {$this->order->order_id} status has been updated to {$this->statusText}",
            'type' => 'order_status_update',
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_id,
            'status' => $this->statusText,
            'message' => "Your order {$this->order->order_id} status has been updated to {$this->statusText}",
            'type' => 'order_status_update',
            'created_at' => now()->toISOString(),
        ]);
    }
}
