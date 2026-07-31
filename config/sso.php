<?php

return [
    // SISFO as an OAuth 2.0 identity provider.
    'url' => rtrim(env('SSO_URL', 'https://sso.smktelkom-lpg.id'), '/'),
    'domain' => env('SSO_DOMAIN', 'sso.smktelkom-lpg.id'),
    'enforce_domain' => env('SSO_ENFORCE_DOMAIN', true),
    'google_redirect_url' => env('SSO_GOOGLE_REDIRECT_URL', 'https://sso.smktelkom-lpg.id/auth/google/callback'),
    'google_workspace_domain' => env('SSO_GOOGLE_WORKSPACE_DOMAIN'),

    // Legacy SISFO-as-client configuration. Kept for the existing disabled
    // "Login SSO STELLA" integration on the main login page.
    'base_url' => rtrim(env('SSO_BASE_URL', 'https://sso.smktelkom-lpg.id'), '/'),
    'client_id' => env('SSO_CLIENT_ID', 'sisfo'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'callback_url' => env('SSO_CALLBACK_URL', env('APP_URL').'/auth/sso/callback'),
];
