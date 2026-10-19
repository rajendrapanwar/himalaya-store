<!doctype html>
<html lang="en-IN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $heading }}</title>
</head>
<body style="margin:0;background:#f5f2ea;color:#243128;font-family:Arial,Helvetica,sans-serif">
<span style="display:none!important;max-height:0;overflow:hidden;opacity:0;color:transparent">{{ $preheader ?? $heading }}</span>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f2ea;padding:28px 12px">
<tr><td align="center">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #e5e1d8;border-radius:18px;overflow:hidden">
    <tr><td style="background:#17382b;padding:26px 34px">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr>
            <td style="width:58px"><img src="{{ asset('assets/img/logo.png') }}" width="48" height="48" alt="Pahadi Roots" style="display:block;border:0;border-radius:14px"></td>
            <td><div style="color:#ffffff;font-family:Georgia,'Times New Roman',serif;font-size:24px;font-weight:700">Pahadi Roots</div><div style="margin-top:4px;color:#cfddd5;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase">From the hills</div></td>
        </tr></table>
    </td></tr>
    <tr><td style="padding:38px 34px 34px">
        @isset($eyebrow)<div style="margin-bottom:12px;color:#b86d0b;font-size:12px;font-weight:800;letter-spacing:1.8px;text-transform:uppercase">{{ $eyebrow }}</div>@endisset
        <h1 style="margin:0;color:#17382b;font-family:Georgia,'Times New Roman',serif;font-size:34px;line-height:1.18">{{ $heading }}</h1>
        @isset($greeting)<p style="margin:25px 0 0;font-size:16px;line-height:1.7">{{ $greeting }}</p>@endisset
        @isset($intro)<p style="margin:14px 0 0;color:#4d5a52;font-size:16px;line-height:1.75">{{ $intro }}</p>@endisset
        @isset($code)<div style="margin:26px 0;border:1px solid #ead9ba;border-radius:14px;background:#fff8e9;padding:20px;text-align:center"><div style="color:#80602b;font-size:11px;font-weight:800;letter-spacing:1.6px;text-transform:uppercase">One-time verification code</div><div style="margin-top:8px;color:#17382b;font-size:36px;font-weight:800;letter-spacing:9px">{{ $code }}</div></div>@endisset
        @if(!empty($details))
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:25px;border-collapse:collapse;border:1px solid #e8e4dc;border-radius:12px;overflow:hidden">
                @foreach($details as $label => $value)<tr><td style="border-bottom:1px solid #eeeae3;padding:12px 15px;color:#6b746e;font-size:13px">{{ $label }}</td><td align="right" style="border-bottom:1px solid #eeeae3;padding:12px 15px;color:#243128;font-size:13px;font-weight:700">{{ $value }}</td></tr>@endforeach
            </table>
        @endif
        @isset($bodyHtml)<div style="margin-top:24px;color:#4d5a52;font-size:15px;line-height:1.75">{!! $bodyHtml !!}</div>@endisset
        @if(!empty($actionUrl) && !empty($actionText))<table role="presentation" cellspacing="0" cellpadding="0" style="margin-top:28px"><tr><td style="border-radius:10px;background:#285d46"><a href="{{ $actionUrl }}" style="display:inline-block;padding:14px 22px;color:#ffffff;font-size:14px;font-weight:800;text-decoration:none">{{ $actionText }} →</a></td></tr></table>@endif
        @isset($outro)<p style="margin:28px 0 0;color:#68736c;font-size:13px;line-height:1.7">{{ $outro }}</p>@endisset
    </td></tr>
    <tr><td style="border-top:1px solid #ece8e0;background:#fbfaf7;padding:23px 34px;color:#747b76;font-size:12px;line-height:1.65">
        <strong style="color:#17382b">Pahadi Roots</strong><br>Rajpur Road, Dehradun, Uttarakhand 248001<br>
        <a href="{{ route('contact') }}" style="color:#285d46;text-decoration:none">Customer care</a> · <a href="{{ route('privacy.policy') }}" style="color:#285d46;text-decoration:none">Privacy policy</a>
    </td></tr>
</table>
<p style="margin:18px 0 0;color:#8a8f8b;font-size:11px;line-height:1.5">This is a service email from Pahadi Roots. Please do not share verification codes or passwords.</p>
</td></tr></table>
</body>
</html>
