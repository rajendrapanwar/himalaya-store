<?php

return [

    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'whatsapp' => [
        'number' => env('WHATSAPP_NUMBER', '919999999999'),
    ],

    'support_email' => env('SUPPORT_EMAIL', 'support@pahadiroots.in'),

    'social' => [
        'instagram' => env('SOCIAL_INSTAGRAM_URL', 'https://www.instagram.com/pahadiroots/'),
        'facebook' => env('SOCIAL_FACEBOOK_URL', 'https://www.facebook.com/pahadiroots/'),
        'youtube' => env('SOCIAL_YOUTUBE_URL', 'https://www.youtube.com/@pahadiroots'),
        'pinterest' => env('SOCIAL_PINTEREST_URL', 'https://www.pinterest.com/pahadiroots/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
