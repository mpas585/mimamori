<?php

return [

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

    'twilio' => [
        'sid'   => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from'  => env('TWILIO_FROM'),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

    'payjp' => [
        'public_key'      => env('PAYJP_PUBLIC_KEY'),
        'secret_key'      => env('PAYJP_SECRET_KEY'),
        'plan_id_monthly' => env('PAYJP_PLAN_ID_MONTHLY'),
        'webhook_secret'  => env('PAYJP_WEBHOOK_SECRET'),
    ],

    'admin' => [
        'notification_email' => env('ADMIN_NOTIFICATION_EMAIL'),
    ],

];
