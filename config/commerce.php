<?php

return [
    'currency' => 'INR',
    'business' => [
        'legal_name' => env('BUSINESS_LEGAL_NAME', 'Pahadi Roots'),
        'address' => env('BUSINESS_ADDRESS', 'Rajpur Road, Dehradun, Uttarakhand 248001'),
        'gstin' => env('BUSINESS_GSTIN'),
    ],
    'stripe_reservation_minutes' => (int) env('STRIPE_RESERVATION_MINUTES', 30),
    'loyalty' => [
        'points_per_rupee' => (float) env('LOYALTY_POINTS_PER_RUPEE', 0.1),
        'rupees_per_point' => (float) env('LOYALTY_RUPEES_PER_POINT', 0.1),
        'maximum_redemption_percent' => (int) env('LOYALTY_MAX_REDEMPTION_PERCENT', 30),
        'referrer_reward' => (int) env('REFERRER_REWARD_POINTS', 100),
        'referred_customer_reward' => (int) env('REFERRED_CUSTOMER_REWARD_POINTS', 50),
    ],
    'whatsapp_number' => env('WHATSAPP_NUMBER', '919876543210'),
];
