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
        return ['database', 'mail'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $order = Order::findOrFail($this->orderId);
        $copy = $this->copy();

        return [
            'title' => $copy[0],
            'message' => $copy[1],
            'action_url' => route('orders.show', $order),
            'category' => 'order',
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = Order::with('address')->findOrFail($this->orderId);
        $copy = $this->copy();
        $details = [
            'Order' => $order->order_number,
            'Order status' => str($order->order_status)->headline(),
            'Payment' => str($order->payment_status)->headline(),
            'Total' => '₹'.number_format((float) $order->total_amount, 2),
        ];
        if ($this->event === 'shipped' && $order->tracking_number) {
            $details['Courier'] = $order->courier_name ?: 'Shipping partner';
            $details['Tracking number'] = $order->tracking_number;
        }

        return (new MailMessage)
            ->subject($copy[0].' · '.$order->order_number)
            ->view(['html' => 'mail.branded-message', 'text' => 'mail.branded-message-text'], [
                'preheader' => $copy[1],
                'eyebrow' => 'Order '.$order->order_number,
                'heading' => $copy[0],
                'greeting' => 'Namaste '.$notifiable->name.',',
                'intro' => $copy[1],
                'details' => $details,
                'actionUrl' => route('orders.show', $order),
                'actionText' => 'View your order',
                'outro' => 'Questions about your order? Reply to this email or contact our Dehradun customer-care team.',
            ]);
    }

    /** @return array{0: string, 1: string} */
    private function copy(): array
    {
        return match ($this->event) {
            'paid' => ['Payment received', 'Your payment has been confirmed and we are preparing your order.'],
            'shipped' => ['Your order is on the way', 'Your Himalayan goods have left our fulfilment centre.'],
            'delivered' => ['Order delivered', 'We hope you enjoy your order. Thank you for supporting mountain producers.'],
            'cancelled' => ['Order cancelled', 'Your order has been cancelled. Any applicable refund is handled according to the original payment method.'],
            'refunded' => ['Refund initiated', 'Your refund has been initiated. Banks can take several business days to display it.'],
            'payment_failed' => ['Payment unsuccessful', 'We could not confirm payment and released the reserved stock. You may place a new order whenever you are ready.'],
            default => ['Order received', 'We have received your order and will keep you updated as it progresses.'],
        };
    }
}
