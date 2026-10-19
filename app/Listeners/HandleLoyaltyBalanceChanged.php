<?php

namespace App\Listeners;

use App\Events\LoyaltyBalanceChanged;
use App\Models\LoyaltyTransaction;
use App\Notifications\AccountActivityNotification;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;

class HandleLoyaltyBalanceChanged implements ShouldQueueAfterCommit
{
    public function handle(LoyaltyBalanceChanged $event): void
    {
        $transaction = LoyaltyTransaction::with('user')->find($event->transactionId);
        if (! $transaction) {
            return;
        }

        $transaction->user->notify(new AccountActivityNotification(
            $transaction->points > 0 ? 'Points added to your account' : 'Loyalty balance updated',
            $transaction->description,
            route('profile').'#points-activity',
            'rewards',
            false,
            ['Points' => ($transaction->points > 0 ? '+' : '').$transaction->points, 'Balance' => $transaction->balance_after],
        ));
    }
}
