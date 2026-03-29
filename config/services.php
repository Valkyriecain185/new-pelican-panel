<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],
    'stripe' => [
    'key'    => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    ],
];
