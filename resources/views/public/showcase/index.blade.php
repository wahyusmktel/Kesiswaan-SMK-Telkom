<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Pameran Karya & Keahlian Siswa SMK Telkom Lampung">
    <title>Showcase Siswa - SMK Telkom Lampung</title>
    <link rel="icon" href="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        window.__SHOWCASE_PAYLOAD__ = @json($payload ?? []);
    </script>
    
    @vite(['resources/js/showcase/main.js'])
</head>
<body class="font-jakarta text-slate-800 antialiased bg-slate-50">
    <div id="showcase-app"></div>
</body>
</html>
