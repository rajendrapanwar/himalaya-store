<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Models\Order;
use App\Services\OrderLifecycleService;
use Illuminate\Console\Command;

class ExpireCommerceReservations extends Command
{
    protected $signature = 'commerce:expire-reservations';

    protected $description = 'Release inventory, coupons and loyalty points held by expired Stripe checkouts';

    public function handle(OrderLifecycleService $lifecycle): int
    {
        $count = 0;
        Order::where('payment_method', 'stripe')
            ->where('payment_status', 'pending')
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '<=', now())
            ->chunkById(100, function ($orders) use ($lifecycle, &$count) {
                foreach ($orders as $order) {
                    $lifecycle->failPayment($order, 'Stripe checkout expired');
                    $count++;
                }
            });

        Cart::whereNull('user_id')->where('updated_at', '<', now()->subDays(30))->delete();
        $this->info("Expired {$count} payment reservation(s).");

        return self::SUCCESS;
    }
}
