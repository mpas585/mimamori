<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'devices'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'devices',
        ],
        'partner' => [
            'driver' => 'session',
            'provider' => 'partners',
        ],
    ],

    'providers' => [
        'devices' => [
            'driver' => 'eloquent',
            'model' => App\Models\Device::class,
        ],
        'partners' => [
            'driver' => 'eloquent',
            'model' => App\Models\PartnerUser::class,
        ],
    ],

    'passwords' => [
        'devices' => [
            'provider' => 'devices',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];


