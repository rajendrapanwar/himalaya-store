<h1>New customer message</h1>
<p><strong>From:</strong> {{ $contactMessage->name }} ({{ $contactMessage->email }})</p>
<p><strong>Phone:</strong> {{ $contactMessage->phone ?: 'Not supplied' }}</p>
<p><strong>Subject:</strong> {{ $contactMessage->subject }}</p>
<p style="white-space:pre-wrap">{{ $contactMessage->message }}</p>
