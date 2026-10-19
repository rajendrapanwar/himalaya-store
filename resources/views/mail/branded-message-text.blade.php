PAHADI ROOTS — FROM THE HILLS

{{ $heading }}

@isset($greeting){{ $greeting }}

@endisset
@isset($intro){{ $intro }}

@endisset
@isset($code)Your one-time verification code: {{ $code }}

@endisset
@if(!empty($details))@foreach($details as $label => $value){{ $label }}: {{ $value }}
@endforeach

@endif
@if(!empty($actionUrl) && !empty($actionText)){{ $actionText }}: {{ $actionUrl }}

@endif
@isset($outro){{ $outro }}

@endisset
Pahadi Roots
Rajpur Road, Dehradun, Uttarakhand 248001
Customer care: {{ route('contact') }}
