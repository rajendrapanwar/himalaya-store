<?php

namespace App\Listeners;

use App\Events\ReturnRequestSubmitted;
use App\Models\ReturnRequest;
use App\Notifications\AccountActivityNotification;
use App\Notifications\AdminAlertNotification;
use App\Services\StaffNotificationService;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;

class HandleReturnRequestSubmitted implements ShouldQueueAfterCommit
{
    public function __construct(private readonly StaffNotificationService $staff) {}

    public function handle(ReturnRequestSubmitted $event): void
    {
        $return = ReturnRequest::with(['order', 'user'])->find($event->returnRequestId);
        if (! $return) {
            return;
        }

        $return->user->notify(new AccountActivityNotification(
            'Return request received',
            "We received your return request for {$return->order->order_number}. Our team will review it shortly.",
            route('orders.show', $return->order),
            'return',
            true,
            ['Order' => $return->order->order_number, 'Status' => 'Requested'],
        ));
        $this->staff->send(new AdminAlertNotification(
            'New return request',
            "{$return->user->name} requested a return for {$return->order->order_number}.",
            route('admin.returns.index', ['status' => 'requested']),
            'return',
        ), ['administrator', 'manager', 'fulfillment', 'support']);
    }
}
