<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
        'drive_scopes' => env('GOOGLE_DRIVE_SCOPES', 'https://www.googleapis.com/auth/drive.file'),
    ],

    'whatsapp_gateway' => [
        'base_url' => env('WHATSAPP_GATEWAY_URL', 'http://127.0.0.1:3001'),
        'api_key' => env('WHATSAPP_GATEWAY_API_KEY'),
    ],

    'cctv' => [
        'mediamtx_api_url' => env('CCTV_MEDIAMTX_API_URL', 'http://127.0.0.1:9997'),
        'mediamtx_api_user' => env('CCTV_MEDIAMTX_API_USER'),
        'mediamtx_api_password' => env('CCTV_MEDIAMTX_API_PASSWORD'),
        'hls_base_url' => env('CCTV_HLS_BASE_URL', 'http://127.0.0.1:8888'),
        'gateway_auth_key' => env('CCTV_GATEWAY_AUTH_KEY'),
        'playback_token_secret' => env('CCTV_PLAYBACK_TOKEN_SECRET', env('APP_KEY')),
        'playback_token_ttl' => env('CCTV_PLAYBACK_TOKEN_TTL', 900),
    ],

];
