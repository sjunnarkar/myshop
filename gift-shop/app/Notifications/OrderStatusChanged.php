<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $oldStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
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
        $message = (new MailMessage)
            ->subject("Order #{$this->order->order_number} Status Updated")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your order #{$this->order->order_number} status has been updated from " . 
                   ucfirst($this->oldStatus) . " to " . ucfirst($this->order->status) . ".");

        // Add specific messages based on the new status
        switch ($this->order->status) {
            case 'processing':
                $message->line('We are now processing your order and will update you once it ships.');
                break;
            case 'shipped':
                $message->line('Your order has been shipped!')
                       ->line('You can track your order using the tracking information that will be sent separately.');
                break;
            case 'delivered':
                $message->line('Your order has been delivered!')
                       ->line('We hope you enjoy your purchase. Please let us know if you have any questions.');
                break;
            case 'cancelled':
                $message->line('Your order has been cancelled.')
                       ->line('If you did not request this cancellation, please contact our support team.');
                break;
        }

        $message->action('View Order Details', route('profile.orders.show', $this->order))
                ->line('Thank you for shopping with us!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->order->status,
        ];
    }
} 