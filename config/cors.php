<?php

$allowedOrigins = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('SPMB_CORS_ALLOWED_ORIGINS', 'https://ppdb.smktelkom-lpg.sch.id,http://localhost:4321,http://127.0.0.1:4321'))
)));

return [
    'paths' => ['api/spmb/*'],
    'allowed_methods' => ['GET', 'OPTIONS'],
    'allowed_origins' => $allowedOrigins,
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Accept', 'Content-Type', 'Origin'],
    'exposed_headers' => [],
    'max_age' => 3600,
    'supports_credentials' => false,
];
