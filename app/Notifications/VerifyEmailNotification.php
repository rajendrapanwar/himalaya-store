<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify your email · Pahadi Roots')
            ->view(['html' => 'mail.branded-message', 'text' => 'mail.branded-message-text'], [
                'preheader' => 'Confirm your email address to complete your Pahadi Roots account.',
                'eyebrow' => 'Welcome to Pahadi Roots',
                'heading' => 'Verify your email address',
                'greeting' => 'Namaste '.$notifiable->name.',',
                'intro' => 'Confirm this email address to secure your account, receive order updates and continue to checkout.',
                'actionUrl' => $url,
                'actionText' => 'Verify email address',
                'outro' => 'If you did not create this account, you can safely ignore this email.',
            ]);
    }
}
