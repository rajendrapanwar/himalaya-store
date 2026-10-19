<?php

namespace App\Listeners;

use App\Events\ReturnRequestStatusChanged;
use App\Models\ReturnRequest;
use App\Notifications\AccountActivityNotification;

class HandleReturnRequestStatusChanged
{
    public function handle(ReturnRequestStatusChanged $event): void
    {
        $return = ReturnRequest::with(['order', 'user'])->find($event->returnRequestId);
        if (! $return) {
            return;
        }

        $copy = match ($return->status) {
            'approved' => ['Return request approved', 'Your request was approved. Please follow the instructions from our support team.'],
            'rejected' => ['Return request reviewed', 'Your return request could not be approved. Please review the team note in your order.'],
            'completed' => ['Return completed', 'Your return request has been completed. Any applicable refund is shown in your order.'],
            default => ['Return request updated', 'There is an update to your return request.'],
        };

        $return->user->notify(new AccountActivityNotification(
            $copy[0],
            $copy[1],
            route('orders.show', $return->order),
            'return',
            true,
            ['Order' => $return->order->order_number, 'Status' => str($return->status)->headline()],
        ));
    }
}
