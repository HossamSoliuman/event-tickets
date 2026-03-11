<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    |
    | Your Stripe API keys. The publishable key is used on the frontend
    | (safe to expose). The secret key is server-side only — never expose it.
    |
    | Get your keys at: https://dashboard.stripe.com/apikeys
    |
    */

    'stripe' => [
        'key'            => env('STRIPE_KEY'),
        'secret'         => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],
];
