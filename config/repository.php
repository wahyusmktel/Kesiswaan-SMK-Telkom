<?php

return [
    'disk' => env('REPOSITORY_DISK', 'repository'),
    'files_directory' => 'files',
    'uploads_directory' => '.uploads',

    // Ukuran chunk kecil menjaga penggunaan memori PHP tetap stabil saat upload ISO besar.
    'chunk_size' => (int) env('REPOSITORY_CHUNK_SIZE', 8 * 1024 * 1024),
    'max_file_size' => (int) env('REPOSITORY_MAX_FILE_SIZE', 100 * 1024 * 1024 * 1024),
    'upload_ttl_hours' => (int) env('REPOSITORY_UPLOAD_TTL_HOURS', 24),

    'allowed_extensions' => [
        'zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz',
        'iso', 'img', 'ova', 'ovf', 'vdi', 'vhd', 'vhdx', 'qcow2',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp',
        'txt', 'csv', 'json', 'xml', 'md',
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'mp3', 'wav', 'mp4', 'mkv', 'avi', 'mov',
        'exe', 'msi', 'apk', 'deb', 'rpm',
    ],

    // Gunakan "nginx" di produksi setelah location internal dikonfigurasi.
    'download_driver' => env('REPOSITORY_DOWNLOAD_DRIVER', 'laravel'),
    'accel_redirect_prefix' => env('REPOSITORY_ACCEL_REDIRECT_PREFIX', '/_protected_repository'),
    'public_base_url' => env('REPOSITORY_PUBLIC_URL', env('APP_URL')),
    'local_base_url' => env('REPOSITORY_LOCAL_URL'),
];
