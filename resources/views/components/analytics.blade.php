{{--
    Loads Google Analytics 4 and/or the Meta Pixel when their IDs are set in .env
    (GA4_MEASUREMENT_ID, META_PIXEL_ID). Without IDs this prints nothing.
    Page events are sent by resources/js/modules/analytics.js.
--}}
@inject('analytics', 'App\Services\Analytics')
@if($analytics->enabled())
<script nonce="{{ Vite::cspNonce() }}">
(() => {
    // Visitors whose browser asks not to be tracked are left alone.
    if (navigator.globalPrivacyControl || navigator.doNotTrack === '1') return;
    const load = (src) => { const script = document.createElement('script'); script.async = true; script.src = src; document.head.appendChild(script); };
@if($analytics->ga4Id())
    window.dataLayer = window.dataLayer || [];
    window.gtag = function () { window.dataLayer.push(arguments); };
    window.gtag('js', new Date());
    window.gtag('config', {{ Js::from($analytics->ga4Id()) }});
    load('https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent({{ Js::from($analytics->ga4Id()) }}));
@endif
@if($analytics->metaPixelId())
    const fbq = window.fbq = function () { fbq.callMethod ? fbq.callMethod.apply(fbq, arguments) : fbq.queue.push(arguments); };
    if (!window._fbq) window._fbq = fbq;
    fbq.push = fbq; fbq.loaded = true; fbq.version = '2.0'; fbq.queue = [];
    load('https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', {{ Js::from($analytics->metaPixelId()) }});
    fbq('track', 'PageView');
@endif
})();
</script>
@endif
