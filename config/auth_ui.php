<?php

/**
 * Authentication UI Configuration
 *
 * File ini menyimpan konfigurasi untuk halaman autentikasi Aplikasi Izin.
 * Ubah nilai di sini untuk customize tampilan dan perilaku halaman login/register.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Color Configuration
    |--------------------------------------------------------------------------
    | Tentukan warna utama yang digunakan di halaman autentikasi
    |
    */
    'colors' => [
        'primary' => [
            'light'   => '#dc2626',  // Warna utama (terang)
            'medium'  => '#991b1b',  // Warna utama (sedang)
            'dark'    => '#7f1d1d',  // Warna utama (gelap)
            'hover'   => '#b91c1c',  // Warna saat hover
        ],
        'status' => [
            'success' => '#10b981',
            'error'   => '#ef4444',
            'warning' => '#f59e0b',
            'info'    => '#3b82f6',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Animation Configuration
    |--------------------------------------------------------------------------
    | Kontrol animasi pada halaman autentikasi
    |
    */
    'animations' => [
        'enabled'          => true,
        'fade_in_duration' => '0.8s',
        'fade_in_easing'   => 'ease-out',
        'hover_duration'   => '0.3s',
        'float_duration'   => '20s',
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Configuration
    |--------------------------------------------------------------------------
    | Konfigurasi untuk form fields
    |
    */
    'forms' => [
        'input' => [
            'border_radius'   => '0.5rem',
            'padding_y'       => '0.75rem',
            'padding_x'       => '1rem',
            'border_color'    => '#d1d5db',
            'focus_color'     => '#dc2626',
        ],
        'button' => [
            'border_radius' => '0.5rem',
            'padding_y'     => '0.75rem',
            'padding_x'     => '1.5rem',
            'height'        => '2.75rem',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Strength Configuration
    |--------------------------------------------------------------------------
    | Tentukan kriteria kekuatan password
    |
    */
    'password' => [
        'min_length'       => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers'  => true,
        'require_special'  => false,
        'show_strength_bar' => true,
        'strength_colors' => [
            'weak'   => '#ef4444',   // Red
            'medium' => '#f59e0b',   // Amber
            'strong' => '#10b981',   // Green
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout Configuration
    |--------------------------------------------------------------------------
    | Konfigurasi layout halaman
    |
    */
    'layout' => [
        'hero_visible_breakpoint' => 'lg',  // Bootstrap breakpoint
        'full_page'              => true,
        'split_ratio'            => '50-50', // hero-form ratio
        'show_mobile_logo'       => true,
        'sticky_navbar'          => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Section Configuration
    |--------------------------------------------------------------------------
    | Konfigurasi untuk hero section di samping form
    |
    */
    'hero' => [
        'enabled'          => true,
        'gradient_enabled' => true,
        'floating_shapes'  => true,
        'show_stats'       => true,
        'stats' => [
            ['number' => '100+', 'label' => 'Sekolah'],
            ['number' => '10K+', 'label' => 'Pengguna'],
            ['number' => '24/7', 'label' => 'Support'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Messages Configuration
    |--------------------------------------------------------------------------
    | Konfigurasi pesan default
    |
    */
    'messages' => [
        'login' => [
            'title'       => 'Masuk',
            'subtitle'    => 'Selamat datang kembali di Aplikasi Izin',
            'button'      => 'Masuk',
            'forgot_link' => 'Lupa password?',
            'register_link' => 'Daftar sekarang',
        ],
        'register' => [
            'title'       => 'Buat Akun Baru',
            'subtitle'    => 'Isi data berikut untuk mendaftar',
            'button'      => 'Daftar Sekarang',
            'login_link'  => 'Masuk di sini',
            'terms_text'  => 'Saya setuju dengan Syarat dan Ketentuan dan Kebijakan Privasi',
        ],
        'forgot' => [
            'title'       => 'Lupa Password?',
            'subtitle'    => 'Masukkan email Anda untuk mendapatkan link reset',
            'button'      => 'Kirim Link Reset',
            'info'        => 'Link reset password akan berlaku selama 1 jam.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Configuration
    |--------------------------------------------------------------------------
    | Konfigurasi validasi form
    |
    */
    'validation' => [
        'email_format'           => true,
        'password_strength'      => true,
        'password_confirmation'  => true,
        'terms_required'         => true,
        'show_inline_errors'     => true,
        'error_message_position' => 'below', // below atau below-inline
    ],

    /*
    |--------------------------------------------------------------------------
    | Icons Configuration
    |--------------------------------------------------------------------------
    | Konfigurasi untuk icons yang ditampilkan
    |
    */
    'icons' => [
        'show_in_inputs'  => true,
        'animate_on_focus' => true,
        'style'           => 'heroicons', // heroicons atau custom
    ],

    /*
    |--------------------------------------------------------------------------
    | Responsive Configuration
    |--------------------------------------------------------------------------
    | Konfigurasi responsive behavior
    |
    */
    'responsive' => [
        'mobile_breakpoint'  => '640px',
        'tablet_breakpoint'  => '1024px',
        'touch_friendly'     => true,
        'prevent_zoom'       => true, // Set font-size 16px on mobile
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    | Konfigurasi keamanan
    |
    */
    'security' => [
        'csrf_protection'       => true,
        'rate_limit'            => true,
        'show_password_strength' => true,
        'hide_username_on_error' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    | Feature toggles untuk percobaan fitur baru
    |
    */
    'features' => [
        'remember_me'           => true,
        'forgot_password'       => true,
        'register'              => true,
        'social_login'          => false,
        'two_factor_auth'       => false,
        'biometric_login'       => false,
        'dark_mode'             => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Configuration
    |--------------------------------------------------------------------------
    | Konfigurasi SEO untuk halaman autentikasi
    |
    */
    'seo' => [
        'login' => [
            'title'       => 'Login - Aplikasi Izin',
            'description' => 'Masuk ke akun Aplikasi Izin Anda untuk mengelola perizinan siswa.',
        ],
        'register' => [
            'title'       => 'Daftar - Aplikasi Izin',
            'description' => 'Buat akun baru Aplikasi Izin untuk memulai mengelola perizinan sekolah.',
        ],
        'forgot' => [
            'title'       => 'Lupa Password - Aplikasi Izin',
            'description' => 'Reset password akun Aplikasi Izin Anda dengan mudah.',
        ],
    ],

];
