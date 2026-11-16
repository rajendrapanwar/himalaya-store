<?php

return [
    'currency' => 'INR',
    'business' => [
        'legal_name' => env('BUSINESS_LEGAL_NAME', 'Pahadi Roots'),
        'address' => env('BUSINESS_ADDRESS', 'Rajpur Road, Dehradun, Uttarakhand 248001'),
        'gstin' => env('BUSINESS_GSTIN'),
    ],
    'stripe_reservation_minutes' => (int) env('STRIPE_RESERVATION_MINUTES', 30),
    'free_shipping_threshold' => (float) env('FREE_SHIPPING_THRESHOLD', 500),
    'loyalty' => [
        'points_per_rupee' => (float) env('LOYALTY_POINTS_PER_RUPEE', 0.1),
        'rupees_per_point' => (float) env('LOYALTY_RUPEES_PER_POINT', 0.1),
        'maximum_redemption_percent' => (int) env('LOYALTY_MAX_REDEMPTION_PERCENT', 30),
        'referrer_reward' => (int) env('REFERRER_REWARD_POINTS', 100),
        'referred_customer_reward' => (int) env('REFERRED_CUSTOMER_REWARD_POINTS', 50),
    ],
    'whatsapp_number' => env('WHATSAPP_NUMBER', '919876543210'),
    // One email to signed-in customers who leave items in their basket. Off until switched on.
    'cart_reminders' => [
        'enabled' => (bool) env('CART_REMINDERS_ENABLED', false),
        'after_hours' => (int) env('CART_REMINDERS_AFTER_HOURS', 4),   // wait this long after the last change
        'max_age_days' => 7,                                           // ignore baskets older than this
        'cooldown_days' => 7,                                          // at most one reminder per basket in this time
    ],
];
