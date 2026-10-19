<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminLoginCode extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your admin login code · Pahadi Roots')
            ->view(['html' => 'mail.branded-message', 'text' => 'mail.branded-message-text'], [
                'preheader' => 'Your one-time administrator verification code expires in 10 minutes.',
                'eyebrow' => 'Secure administration',
                'heading' => 'Finish signing in',
                'greeting' => 'Namaste '.$notifiable->name.',',
                'intro' => 'Use the one-time code below to verify this administrator sign-in. It expires in 10 minutes and can only be used once.',
                'code' => $this->code,
                'outro' => 'If you did not try to sign in, change your password immediately and review staff access.',
            ]);
    }
}
