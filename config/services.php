<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'sp2kp' => [
        'url' => env('SP2KP_URL', 'https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-daily-json'),
        'province_code' => env('SP2KP_PROVINCE_CODE', '17'),
        'province_name' => env('SP2KP_PROVINCE_NAME', 'Bengkulu'),
        'regency_code' => env('SP2KP_REGENCY_CODE', '1709'),
        'regency_name' => env('SP2KP_REGENCY_NAME', 'Bengkulu Tengah'),
        'timeout' => (int) env('SP2KP_TIMEOUT', 30),
    ],

];
