<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SISFO TS - Layanan sekolah terintegrasi SMK Telkom Lampung.">
    <title>{{ $setting->school_name ?: 'SMK Telkom Lampung' }} - SISFO TS</title>
    @if ($setting->favicon)
        <link rel="icon" href="{{ asset('storage/'.ltrim($setting->favicon, '/')) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <script>window.__STELLA_LANDING__ = {{ Illuminate\Support\Js::from($payload) }};</script>
    @vite('resources/js/landing/main.js')
</head>
<body>
    <div id="stella-vue-landing"></div>
</body>
</html>
