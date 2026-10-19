<?php

namespace App\Listeners;

use App\Events\ContactMessageSubmitted;
use App\Mail\ContactMessageAcknowledgement;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Notifications\AccountActivityNotification;
use App\Notifications\AdminAlertNotification;
use App\Services\StaffNotificationService;
use Illuminate\Support\Facades\Mail;

class HandleContactMessageSubmitted
{
    public function __construct(private readonly StaffNotificationService $staff) {}

    public function handle(ContactMessageSubmitted $event): void
    {
        $message = ContactMessage::find($event->contactMessageId);
        if (! $message) {
            return;
        }

        Mail::to(config('services.support_email'))->queue(new ContactMessageReceived($message));
        Mail::to($message->email)->queue(new ContactMessageAcknowledgement($message));
        $message->user?->notify(new AccountActivityNotification(
            'Message received',
            "We received your message about {$message->subject} and will reply shortly.",
            route('contact'),
            'contact',
        ));
        $this->staff->send(new AdminAlertNotification(
            'New contact request',
            "{$message->name} sent a message about {$message->subject}.",
            route('admin.contact-messages.index'),
            'contact',
        ), ['administrator', 'manager', 'support']);
    }
}
