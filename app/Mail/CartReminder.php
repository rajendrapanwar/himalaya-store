<?php

namespace App\Mail;

use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/** One reminder to a signed-in customer who left items in their basket. */
class CartReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Cart $cart) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your basket is waiting · Pahadi Roots');
    }

    public function headers(): Headers
    {
        // Lets Gmail and others show their own "unsubscribe" button.
        return new Headers(text: ['List-Unsubscribe' => '<'.$this->unsubscribeUrl().'>']);
    }

    public function content(): Content
    {
        $unsubscribeUrl = $this->unsubscribeUrl();

        return new Content(view: 'mail.branded-message', text: 'mail.branded-message-text', with: [
            'preheader' => 'The items you picked are still in stock.',
            'eyebrow' => 'Your basket',
            'heading' => 'You left something in your basket',
            'greeting' => 'Namaste '.$this->cart->user->name.',',
            'intro' => 'The items you picked are saved and still in stock. You can finish your order in a minute.',
            'details' => self::lines($this->cart),
            'actionUrl' => route('checkout.index'),
            'actionText' => 'Finish my order',
            'bodyHtml' => '<p style="margin:0;font-size:12px;color:#68736c">Do not want basket reminders? <a href="'.e($unsubscribeUrl).'" style="color:#285d46">Stop these emails</a>.</p>',
            'outro' => 'You received this because you added items to your basket at Pahadi Roots. To stop basket reminders, open: '.$unsubscribeUrl,
        ]);
    }

    /**
     * The basket lines that can still be bought, as "Name × quantity" => "₹ price".
     *
     * @return array<string, string>
     */
    public static function lines(Cart $cart): array
    {
        $lines = [];
        foreach ($cart->items as $item) {
            $product = $item->product;
            if ($product && $product->status && $product->available_stock > 0) {
                $lines["{$product->name} × {$item->quantity}"] = '₹'.number_format((float) ($product->sale_price ?? $product->price) * $item->quantity, 2);
            }
        }
        foreach ($cart->bundleItems as $item) {
            $bundle = $item->bundle;
            if ($bundle && $bundle->is_active && $bundle->available_stock > 0) {
                $lines["{$bundle->name} (gift box) × {$item->quantity}"] = '₹'.number_format((float) $bundle->price * $item->quantity, 2);
            }
        }

        return $lines;
    }

    private function unsubscribeUrl(): string
    {
        return URL::signedRoute('cart-reminders.unsubscribe', ['user' => $this->cart->user_id]);
    }
}
