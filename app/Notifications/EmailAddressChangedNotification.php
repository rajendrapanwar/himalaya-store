<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Sent to the previous address when an account email changes, so the real owner
 * learns about a possible takeover even though the account now points elsewhere.
 */
class EmailAddressChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $customerName, public string $newEmail)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your account email was changed · Pahadi Roots')
            ->view(['html' => 'mail.branded-message', 'text' => 'mail.branded-message-text'], [
                'preheader' => 'The email address on your Pahadi Roots account was changed.',
                'eyebrow' => 'Account security',
                'heading' => 'Your account email was changed',
                'greeting' => 'Namaste '.$this->customerName.',',
                'intro' => 'The sign-in email for your Pahadi Roots account was changed from this address. Future order updates will go to the new address shown below.',
                'details' => [
                    'New email' => self::mask($this->newEmail),
                    'Changed at' => now()->format('d M Y, h:i A'),
                ],
                'actionUrl' => route('contact'),
                'actionText' => 'This wasn’t me',
                'outro' => 'If you made this change, no action is needed. If not, contact our customer-care team immediately so we can secure your account.',
            ]);
    }

    private static function mask(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        return Str::mask($local, '•', min(2, mb_strlen($local))).'@'.$domain;
    }
}
