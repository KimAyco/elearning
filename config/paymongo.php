<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PayMongo API
    |--------------------------------------------------------------------------
    | Get your API keys at: https://dashboard.paymongo.com/developers
    */

    'secret_key' => env('PAYMONGO_SECRET_KEY', ''),
    'public_key' => env('PAYMONGO_PUBLIC_KEY', ''),
    'api_url'    => env('PAYMONGO_API_URL', 'https://api.paymongo.com/v1'),

    // Only these methods are offered (Payment Intent API)
    'allowed_payment_methods' => ['gcash', 'paymaya'],

    // Webhook secret (set after creating a webhook in PayMongo dashboard)
    'webhook_secret' => env('PAYMONGO_WEBHOOK_SECRET', ''),
];
