PAHADI ROOTS — CUSTOMER CARE

Namaste {{ $contactMessage->name }},

Thank you for writing to us about “{{ $contactMessage->subject }}”. Our Dehradun team will review your message and normally respond within one business day.

Reference: PR-CONTACT-{{ $contactMessage->id }}
Received: {{ $contactMessage->created_at->format('d M Y, h:i A') }}

Customer care: {{ route('contact') }}
