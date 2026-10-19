PAHADI ROOTS — NEW CUSTOMER MESSAGE

From: {{ $contactMessage->name }}
Email: {{ $contactMessage->email }}
Phone: {{ $contactMessage->phone ?: 'Not supplied' }}
Subject: {{ $contactMessage->subject }}

{{ $contactMessage->message }}

Open contact requests: {{ route('admin.contact-messages.index') }}
