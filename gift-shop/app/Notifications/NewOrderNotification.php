<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $itemCount = $this->order->items->sum('quantity');
        
        return (new MailMessage)
            ->subject("New Order #{$this->order->order_number}")
            ->greeting('Hello Admin,')
            ->line("A new order has been placed.")
            ->line("Order Details:")
            ->line("- Order Number: #{$this->order->order_number}")
            ->line("- Customer: {$this->order->user->name}")
            ->line("- Items: {$itemCount}")
            ->line("- Total Amount: " . config('app.currency_symbol') . number_format($this->order->total, 2))
            ->line("- Payment Method: " . ucfirst($this->order->payment_method))
            ->line("- Payment Status: " . ucfirst($this->order->payment_status))
            ->action('View Order Details', route('admin.orders.show', $this->order))
            ->line('Please process this order as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->user->name,
            'total' => $this->order->total,
        ];
    }
} 