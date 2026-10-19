<?php

namespace App\Listeners;

use App\Events\ReviewModerated;
use App\Models\Review;
use App\Notifications\AccountActivityNotification;

class HandleReviewModerated
{
    public function handle(ReviewModerated $event): void
    {
        $review = Review::with(['product', 'user'])->find($event->reviewId);
        if (! $review || $review->moderation_status === 'pending') {
            return;
        }

        $approved = $review->moderation_status === 'approved';
        $review->user->notify(new AccountActivityNotification(
            $approved ? 'Your review is now live' : 'An update about your review',
            $approved
                ? "Your review of {$review->product->name} was approved and is now visible to shoppers."
                : "Your review of {$review->product->name} could not be published. You can contact us if you would like more information.",
            route('products.show', $review->product->slug).'#reviews',
            'review',
            true,
        ));
    }
}
