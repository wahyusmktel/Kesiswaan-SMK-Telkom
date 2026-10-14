@echo off
setlocal enabledelayedexpansion
title SISFO SMK Telkom Lampung - Local Development

:: Masuk ke direktori tempat script ini berada
cd /d "%~dp0"

:: Pastikan path Node.js, NPM, dan PHP selalu tersedia di sesi terminal ini
if exist "C:\Program Files\nodejs" set "PATH=C:\Program Files\nodejs;%PATH%"
if exist "%APPDATA%\npm" set "PATH=%APPDATA%\npm;%PATH%"
if exist "C:\newappraporsmk\php" set "PATH=C:\newappraporsmk\php;%PATH%"

echo =====================================================================
echo       SISFO SMK TELKOM LAMPUNG - LOCAL DEVELOPMENT SERVER
echo =====================================================================
echo.

:: 1. Periksa apakah PHP terpasang
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] PHP tidak terdeteksi di PATH sistem kamu!
    echo Pastikan PHP ^(minimal versi 8.2^) sudah terinstal.
    echo.
    pause
    exit /b 1
)

:: 2. Periksa apakah Node.js terpasang
where node >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Node.js ^(node.exe^) tidak terdeteksi di sistem kamu!
    echo Pastikan Node.js sudah terinstal dari https://nodejs.org/
    echo.
    pause
    exit /b 1
)

:: 3. Periksa apakah NPM terpasang
where npm >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] NPM tidak terdeteksi di sistem kamu!
    echo Pastikan Node.js ^& NPM sudah terinstal dari https://nodejs.org/
    echo.
    pause
    exit /b 1
)

:: 4. Periksa file .env
if not exist ".env" (
    echo [INFO] File .env belum ada. Membuat dari .env.example...
    if exist ".env.example" (
        copy .env.example .env >nul
        call php artisan key:generate
        echo [OK] File .env berhasil dibuat.
    ) else (
        echo [ERROR] File .env dan .env.example tidak ditemukan!
        pause
        exit /b 1
    )
)

:: 5. Info Database
findstr /i "DB_CONNECTION=mysql" .env >nul 2>nul
if %errorlevel% equ 0 (
    echo [DB] Menggunakan database MariaDB / MySQL ^(aplikasi_izin^)
) else (
    findstr /i "DB_CONNECTION=sqlite" .env >nul 2>nul
    if %errorlevel% equ 0 (
        if not exist "database\database.sqlite" (
            echo [INFO] Menyiapkan database SQLite baru...
            type nul > database\database.sqlite
            call php artisan migrate --force
        )
        echo [DB] Menggunakan database SQLite ^(database/database.sqlite^)
    )
)

echo [1/2] Server Laravel   : http://127.0.0.1:8000
echo [2/2] Vite Dev Server  : http://127.0.0.1:5173
echo.
echo Membuka browser otomatis dalam 3 detik...
echo Tekan [Ctrl + C] untuk menghentikan server.
echo =====================================================================
echo.

:: Buka browser otomatis ke localhost:8000
start "" cmd /c "timeout /t 3 /nobreak >nul && start http://127.0.0.1:8000"

:: Jalankan Laravel dan Vite secara bersamaan
if exist "node_modules\concurrently" (
    call npm run dev:all
) else (
    echo [INFO] Menjalankan server di jendela terpisah...
    start "SISFO - Laravel Server" cmd /k "php artisan serve"
    start "SISFO - Vite Assets" cmd /k "npm run dev"
)

echo.
echo Server telah dihentikan.
pause
