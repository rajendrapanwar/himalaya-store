<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;

    public function toMail($notifiable): MailMessage
    {
        $url = route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]);

        return (new MailMessage)
            ->subject('Reset your password · Pahadi Roots')
            ->view(['html' => 'mail.branded-message', 'text' => 'mail.branded-message-text'], [
                'preheader' => 'Use this secure link to reset your Pahadi Roots password.',
                'eyebrow' => 'Account security',
                'heading' => 'Reset your password',
                'greeting' => 'Namaste '.$notifiable->name.',',
                'intro' => 'We received a request to reset the password for your account. The secure link below expires in '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' minutes.',
                'actionUrl' => $url,
                'actionText' => 'Choose a new password',
                'outro' => 'If you did not request a password reset, no action is required. Your password has not changed.',
            ]);
    }
}
