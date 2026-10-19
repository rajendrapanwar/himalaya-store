@include('mail.branded-message', [
    'preheader' => 'Your message has reached the Pahadi Roots customer-care team.',
    'eyebrow' => 'Customer care',
    'heading' => 'We received your message',
    'greeting' => 'Namaste '.$contactMessage->name.',',
    'intro' => 'Thank you for writing to us about “'.$contactMessage->subject.'”. Our Dehradun team will review your message and normally respond within one business day.',
    'details' => [
        'Reference' => 'PR-CONTACT-'.$contactMessage->id,
        'Received' => $contactMessage->created_at->format('d M Y, h:i A'),
    ],
    'actionUrl' => route('contact'),
    'actionText' => 'Visit customer care',
    'outro' => 'You can reply to this email if you need to add anything to your request.',
])
