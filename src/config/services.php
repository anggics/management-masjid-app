<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // --- Integrasi pihak ketiga aplikasi masjid ---
    'aladhan' => [
        'base_url' => env('ALADHAN_BASE_URL', 'https://api.aladhan.com/v1'),
        'city' => env('PRAYER_CITY', 'Jakarta'),
        'country' => env('PRAYER_COUNTRY', 'Indonesia'),
        'method' => env('PRAYER_METHOD', 20),
    ],

    'cloudinary' => [
        'url' => env('CLOUDINARY_URL'),
    ],

    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'),
        'project_id' => env('FCM_PROJECT_ID'),
    ],

];
