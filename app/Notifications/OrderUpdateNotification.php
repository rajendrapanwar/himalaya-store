<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $orderId, public string $event)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = Order::with('address')->findOrFail($this->orderId);
        $copy = match ($this->event) {
            'paid' => ['Payment received', 'Your payment has been confirmed and we are preparing your order.'],
            'shipped' => ['Your order is on the way', 'Your Himalayan goods have left our fulfilment centre.'],
            'delivered' => ['Order delivered', 'We hope you enjoy your order. Thank you for supporting mountain producers.'],
            'cancelled' => ['Order cancelled', 'Your order has been cancelled. Any applicable refund is handled according to the original payment method.'],
            'refunded' => ['Refund initiated', 'Your refund has been initiated. Banks can take several business days to display it.'],
            'payment_failed' => ['Payment unsuccessful', 'We could not confirm payment and released the reserved stock. You may place a new order whenever you are ready.'],
            default => ['Order received', 'We have received your order and will keep you updated as it progresses.'],
        };

        $mail = (new MailMessage)
            ->subject($copy[0].' · '.$order->order_number)
            ->greeting('Namaste '.$notifiable->name.',')
            ->line($copy[1])
            ->line('Order: '.$order->order_number)
            ->line('Total: ₹'.number_format((float) $order->total_amount, 2));

        if ($this->event === 'shipped' && $order->tracking_number) {
            $mail->line('Courier: '.($order->courier_name ?: 'Shipping partner'))
                ->line('Tracking number: '.$order->tracking_number);
        }

        return $mail->action('View order', route('orders.show', $order))
            ->line('Pahadi Roots · Dehradun, Uttarakhand');
    }
}
