<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Plantix AI — Application Constants
    |--------------------------------------------------------------------------
    */

    // Tax rate applied to every order (0.0 = 0%, 0.1 = 10%)
    'tax_rate' => (float) env('PLANTIX_TAX_RATE', 0.0),

    // Maximum items allowed in a single cart
    'cart_max_items' => (int) env('PLANTIX_CART_MAX_ITEMS', 50),

    // Low-stock alert threshold (units)
    'low_stock_threshold' => (int) env('PLANTIX_LOW_STOCK_THRESHOLD', 10),

    // Per-page pagination defaults
    'paginate' => [
        'products'     => 20,
        'orders'       => 15,
        'appointments' => 15,
        'forum'        => 20,
    ],

    // Currency
    'currency_code'   => env('PLANTIX_CURRENCY_CODE', 'PKR'),
    'currency_symbol' => env('PLANTIX_CURRENCY_SYMBOL', 'Rs'),

    // Featured products shown on homepage
    'homepage_featured_count' => (int) env('PLANTIX_FEATURED_COUNT', 8),

    // Appointment slots per day per expert (used for availability check)
    'appointments_per_day' => (int) env('PLANTIX_APPOINTMENTS_PER_DAY', 10),

    // Admin commission percentage (0–100)
    'admin_commission' => (float) env('PLANTIX_ADMIN_COMMISSION', 10.0),

    // Refund wallet credit validity (days)
    'wallet_credit_validity_days' => (int) env('PLANTIX_WALLET_VALIDITY', 365),

];
