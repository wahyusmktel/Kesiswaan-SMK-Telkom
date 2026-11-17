# Smart TV Kiosk Client - SMK Telkom Lampung

Aplikasi desktop berbasis Python & PyQt6 Chromium WebEngine untuk menjalankan display Smart TV Kiosk di PC Client secara otomatis, layar penuh (borderless), dan aman.

## Fitur Utama
1. **Layar Penuh (Borderless Kiosk Mode):** Berjalan di atas seluruh taskbar dan aplikasi lain (`FramelessWindowHint` & `WindowStaysOnTopHint`).
2. **Kombinasi Keyboard Rahasia:** Aplikasi tidak dapat ditutup dengan tombol biasa (`Alt + F4`, `Esc`, `Ctrl + W`, `Ctrl + Q` diblokir).
   - **Kombinasi Penutupan:** Tekan berurutan / bersamaan: **`Ctrl + C + B`**
3. **Auto Hide Cursor:** Kursor mouse otomatis disembunyikan jika tidak digerakkan selama 3 detik.
4. **Auto-Startup PC:** Otomatis berjalan saat komputer dinyalakan atau login Windows.
5. **Konfigurasi Mudah:** Cukup edit file `config.json` di sebelah file `SmartTvKiosk.exe`.

## Cara Pemasangan di PC Client Smart TV
1. Salin seluruh isi folder `dist/` (`SmartTvKiosk.exe` dan `config.json`) ke PC Client Smart TV (misalnya di `C:\SmartTV\`).
2. Buka file `config.json` dan sesuaikan IP/Domain URL Sisfo, contoh:
   ```json
   {
       "tv_url": "https://sisfo.smktelkom-lpg.id/tv/jadwal",
       "fullscreen": true,
       "stay_on_top": true,
       "hide_cursor_seconds": 3,
       "auto_startup": true,
       "title": "Smart TV Kiosk - SMK Telkom Lampung"
   }
   ```
3. Klik 2x pada `SmartTvKiosk.exe`. Aplikasi akan langsung aktif layar penuh dan otomatis mendaftarkan dirinya ke Windows Startup (sehingga setiap PC dinyalakan, Smart TV langsung tampil).
4. Untuk menutup aplikasi, tekan: **`Ctrl + C + B`**.
