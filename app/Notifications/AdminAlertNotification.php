<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $actionUrl,
        public string $category = 'operations',
        public bool $sendMail = false,
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return $this->sendMail ? ['database', 'mail'] : ['database'];
    }

    /** @return array<string, string> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
            'category' => $this->category,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title.' · Pahadi Roots Admin')
            ->view(['html' => 'mail.branded-message', 'text' => 'mail.branded-message-text'], [
                'preheader' => $this->message,
                'eyebrow' => 'Admin notification',
                'heading' => $this->title,
                'greeting' => 'Namaste '.$notifiable->name.',',
                'intro' => $this->message,
                'actionUrl' => $this->actionUrl,
                'actionText' => 'Open in admin',
                'outro' => 'This operational notification was generated automatically by Pahadi Roots.',
            ]);
    }
}
