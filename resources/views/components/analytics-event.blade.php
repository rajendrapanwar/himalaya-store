{{--
    Queues one analytics event for the page, for example:
    <x-analytics-event name="view_item" :params="[...]" />
    Give a "dedupe" key to events that must be counted a single time even if
    the page is reloaded (a purchase). The JSON is data, not code, so it needs no script nonce.
--}}
@props(['name', 'params' => [], 'dedupe' => null])
@inject('analytics', 'App\Services\Analytics')
@if($analytics->enabled())
<script type="application/json" data-analytics-event>{!! json_encode(['name' => $name, 'params' => $params, 'once' => $dedupe], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
