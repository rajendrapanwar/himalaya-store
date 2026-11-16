<?php

namespace App\Console\Commands;

use App\Mail\CartReminder;
use App\Models\Cart;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCartReminders extends Command
{
    protected $signature = 'carts:send-reminders {--dry-run : List the baskets that would be emailed, without sending anything}';

    protected $description = 'Email signed-in customers who left items in their basket (one reminder per basket)';

    public function handle(): int
    {
        $settings = config('commerce.cart_reminders');
        $dryRun = (bool) $this->option('dry-run');
        if (! $settings['enabled'] && ! $dryRun) {
            $this->info('Basket reminders are switched off. Set CART_REMINDERS_ENABLED=true to send them.');

            return self::SUCCESS;
        }

        $now = now();
        $quietSince = $now->copy()->subHours((int) $settings['after_hours']);
        $oldest = $now->copy()->subDays((int) $settings['max_age_days']);
        $sent = 0;

        Cart::query()
            ->whereNotNull('user_id')
            ->where(fn ($query) => $query->whereNull('reminded_at')->orWhere('reminded_at', '<=', $now->copy()->subDays((int) $settings['cooldown_days'])))
            // Only verified customers who have not opted out; never staff.
            ->whereHas('user', fn ($query) => $query->whereNotNull('email_verified_at')->whereNull('cart_reminders_unsubscribed_at')->where('is_admin', false)->where('role', 'customer'))
            ->where(fn ($query) => $query->whereHas('items')->orWhereHas('bundleItems'))
            ->with(['user', 'items.product', 'bundleItems.bundle.products'])
            ->chunkById(100, function ($carts) use ($quietSince, $oldest, $dryRun, $now, &$sent) {
                foreach ($carts as $cart) {
                    $lastActivity = collect([$cart->items->max('updated_at'), $cart->bundleItems->max('updated_at')])->filter()->max();
                    // Skip baskets still being filled, very old ones, and ones with nothing new since the last reminder.
                    if (! $lastActivity || $lastActivity->gt($quietSince) || $lastActivity->lt($oldest)) {
                        continue;
                    }
                    if ($cart->reminded_at && $cart->reminded_at->gte($lastActivity)) {
                        continue;
                    }
                    if (CartReminder::lines($cart) === []) {
                        continue; // nothing in the basket can be bought any more
                    }

                    if ($dryRun) {
                        $this->line("Would remind {$cart->user->email} about basket #{$cart->id}");
                    } else {
                        Mail::to($cart->user->email)->queue(new CartReminder($cart));
                        // Leave updated_at alone: CartService uses it to pick the customer's current basket.
                        $cart->timestamps = false;
                        $cart->forceFill(['reminded_at' => $now])->saveQuietly();
                    }
                    $sent++;
                }
            });

        $this->info(($dryRun ? 'Baskets that would be reminded: ' : 'Basket reminders sent: ').$sent);

        return self::SUCCESS;
    }
}
