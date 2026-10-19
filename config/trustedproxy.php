<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trusted proxies
    |--------------------------------------------------------------------------
    |
    | When the application runs behind a load balancer, CDN or reverse proxy,
    | the connecting address is the proxy rather than the customer. Without
    | trusting it, every visitor shares one IP address for rate limiting and
    | HTTPS is not detected. Use a comma-separated list of proxy addresses or
    | CIDR ranges, or "*" only when the origin accepts traffic exclusively
    | from the proxy. Leave empty when PHP receives connections directly.
    |
    */

    'proxies' => env('TRUSTED_PROXIES') ?: null,
];
