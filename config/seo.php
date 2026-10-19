<?php

return [
    'site_name' => env('SEO_SITE_NAME', env('APP_NAME', 'Pahadi Roots')),
    'default_title' => env('SEO_DEFAULT_TITLE', 'Pahadi Roots | Himalayan Food from Uttarakhand'),
    'default_description' => env('SEO_DEFAULT_DESCRIPTION', 'Shop traceable Himalayan honey, grains, pulses, teas, spices, preserves and craft, packed with care in Dehradun.'),
    'locale' => 'en_IN',
    'language' => 'en-IN',
    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    'bing_site_verification' => env('BING_SITE_VERIFICATION'),
    'twitter_site' => env('SEO_TWITTER_SITE'),
    'default_social_image' => env('SEO_DEFAULT_SOCIAL_IMAGE', 'assets/img/hero/himalayan-collection-v2.webp'),
    'organization' => [
        'name' => env('BUSINESS_LEGAL_NAME', 'Pahadi Roots'),
        'email' => env('SUPPORT_EMAIL', 'support@pahadiroots.in'),
        'telephone' => env('BUSINESS_PHONE'),
        'street_address' => env('BUSINESS_STREET_ADDRESS', 'Rajpur Road'),
        'locality' => env('BUSINESS_LOCALITY', 'Dehradun'),
        'region' => env('BUSINESS_REGION', 'Uttarakhand'),
        'postal_code' => env('BUSINESS_POSTAL_CODE', '248001'),
        'country' => 'IN',
        'legal_name' => env('BUSINESS_LEGAL_NAME', 'Pahadi Roots'),
        'founding_date' => env('BUSINESS_FOUNDING_DATE'),
    ],
];
