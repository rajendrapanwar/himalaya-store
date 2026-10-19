@include('mail.branded-message', [
    'preheader' => 'A new customer contact request needs attention.',
    'eyebrow' => 'Customer care',
    'heading' => 'New contact request',
    'greeting' => 'Namaste Pahadi Roots team,',
    'intro' => 'A customer has sent a message through the website contact form.',
    'details' => [
        'From' => $contactMessage->name,
        'Email' => $contactMessage->email,
        'Phone' => $contactMessage->phone ?: 'Not supplied',
        'Subject' => $contactMessage->subject,
    ],
    'bodyHtml' => '<div style="border-left:4px solid #d78a18;background:#fbfaf7;padding:16px 18px;white-space:pre-wrap">'.e($contactMessage->message).'</div>',
    'actionUrl' => route('admin.contact-messages.index'),
    'actionText' => 'Open contact requests',
    'outro' => 'Reply directly to this email to respond to the customer.',
])
