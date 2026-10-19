<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountActivityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @param array<string, string|int|float> $details */
    public function __construct(
        public string $title,
        public string $message,
        public string $actionUrl,
        public string $category = 'account',
        public bool $sendMail = false,
        public array $details = [],
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return $this->sendMail ? ['database', 'mail'] : ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
            'category' => $this->category,
            'details' => $this->details,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title.' · Pahadi Roots')
            ->view(['html' => 'mail.branded-message', 'text' => 'mail.branded-message-text'], [
                'preheader' => $this->message,
                'eyebrow' => 'Account update',
                'heading' => $this->title,
                'greeting' => 'Namaste '.$notifiable->name.',',
                'intro' => $this->message,
                'details' => $this->details,
                'actionUrl' => $this->actionUrl,
                'actionText' => 'View details',
                'outro' => 'Thank you for being part of Pahadi Roots and supporting Himalayan producers.',
            ]);
    }
}
