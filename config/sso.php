<?php

return [
    'base_url' => rtrim(env('SSO_BASE_URL', 'https://sso.smktelkom-lpg.id'), '/'),
    'client_id' => env('SSO_CLIENT_ID', 'sisfo'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'callback_url' => env('SSO_CALLBACK_URL', env('APP_URL').'/auth/sso/callback'),
];
