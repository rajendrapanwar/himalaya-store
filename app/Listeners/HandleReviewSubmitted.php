<?php

namespace App\Listeners;

use App\Events\ReviewSubmitted;
use App\Models\Review;
use App\Notifications\AdminAlertNotification;
use App\Services\StaffNotificationService;

class HandleReviewSubmitted
{
    public function __construct(private readonly StaffNotificationService $staff) {}

    public function handle(ReviewSubmitted $event): void
    {
        $review = Review::with(['product', 'user'])->find($event->reviewId);
        if (! $review) {
            return;
        }

        $this->staff->send(new AdminAlertNotification(
            'Review awaiting moderation',
            "{$review->user->name} submitted a {$review->rating}-star review for {$review->product->name}.",
            route('admin.reviews.index', ['status' => 'pending']),
            'review',
        ), ['administrator', 'manager', 'support']);
    }
}
