"""
Smart TV Kiosk Application - SMK Telkom Lampung
Menampilkan halaman Smart TV Jadwal & Mading Digital secara Fullscreen Kiosk.
Hanya dapat ditutup dengan kombinasi keyboard: Ctrl + C + B
"""

import sys
import os
import json
import time
import winreg
from PyQt6.QtCore import Qt, QUrl, QTimer, QEvent, QObject
from PyQt6.QtWidgets import QApplication, QMainWindow
from PyQt6.QtWebEngineWidgets import QWebEngineView
from PyQt6.QtWebEngineCore import QWebEngineSettings, QWebEnginePage

DEFAULT_CONFIG = {
    "tv_url": "https://sisfo.smktelkom-lpg.id/tv/jadwal",
    "fullscreen": True,
    "stay_on_top": True,
    "hide_cursor_seconds": 3,
    "auto_startup": True,
    "title": "Smart TV Kiosk - SMK Telkom Lampung"
}

def get_base_dir():
    if getattr(sys, 'frozen', False):
        return os.path.dirname(sys.executable)
    return os.path.dirname(os.path.abspath(__file__))

def load_config():
    base_dir = get_base_dir()
    config_path = os.path.join(base_dir, "config.json")
    if not os.path.exists(config_path):
        try:
            with open(config_path, "w", encoding="utf-8") as f:
                json.dump(DEFAULT_CONFIG, f, indent=4)
        except Exception as e:
            print(f"Gagal menulis config default: {e}")
        return DEFAULT_CONFIG.copy()

    try:
        with open(config_path, "r", encoding="utf-8") as f:
            data = json.load(f)
            merged = DEFAULT_CONFIG.copy()
            merged.update(data)
            return merged
    except Exception as e:
        print(f"Gagal membaca config: {e}")
        return DEFAULT_CONFIG.copy()

def set_windows_startup(enable=True):
    """Mendaftarkan atau menghapus aplikasi dari Windows Startup Registry"""
    try:
        app_path = sys.executable if getattr(sys, 'frozen', False) else os.path.abspath(__file__)
        key = winreg.OpenKey(
            winreg.HKEY_CURRENT_USER,
            r"Software\Microsoft\Windows\CurrentVersion\Run",
            0,
            winreg.KEY_ALL_ACCESS
        )
        app_name = "StellaSmartTvKiosk"
        if enable:
            winreg.SetValueEx(key, app_name, 0, winreg.REG_SZ, f'"{app_path}"')
            print("Windows Startup: Berhasil diaktifkan.")
        else:
            try:
                winreg.DeleteValue(key, app_name)
                print("Windows Startup: Berhasil dinonaktifkan.")
            except FileNotFoundError:
                pass
        winreg.CloseKey(key)
    except Exception as e:
        print(f"Gagal mengatur Windows Startup registry: {e}")

class CustomWebEnginePage(QWebEnginePage):
    """Custom Page untuk menangani sertifikat dan full screen"""
    def certificateError(self, certificateError):
        # Abaikan certificate error di lingkungan LAN / SSL lokal jika ada
        return True

class KioskWindow(QMainWindow):
    def __init__(self, config):
        super().__init__()
        self.config = config
        self.setWindowTitle(config.get("title", "Smart TV Kiosk"))
        
        # State kombinasi tombol rahasia (Ctrl + C + B)
        self.ctrl_c_timestamp = 0

        # Inisialisasi Browser View
        self.browser = QWebEngineView(self)
        self.custom_page = CustomWebEnginePage(self.browser)
        self.browser.setPage(self.custom_page)

        # Pengaturan WebEngine
        settings = self.browser.settings()
        settings.setAttribute(QWebEngineSettings.WebAttribute.FullScreenSupportEnabled, True)
        settings.setAttribute(QWebEngineSettings.WebAttribute.JavascriptEnabled, True)
        settings.setAttribute(QWebEngineSettings.WebAttribute.LocalStorageEnabled, True)
        settings.setAttribute(QWebEngineSettings.WebAttribute.ScrollAnimatorEnabled, True)
        settings.setAttribute(QWebEngineSettings.WebAttribute.PluginsEnabled, True)

        self.setCentralWidget(self.browser)

        # Window Flags untuk Kiosk Fullscreen
        window_flags = Qt.WindowType.FramelessWindowHint
        if self.config.get("stay_on_top", True):
            window_flags |= Qt.WindowType.WindowStaysOnTopHint
        self.setWindowFlags(window_flags)

        # Sembunyikan mouse kursor otomatis
        self.hide_cursor_timer = QTimer(self)
        self.hide_cursor_timer.setSingleShot(True)
        self.hide_cursor_timer.timeout.connect(self.hide_cursor)
        self.hide_cursor_seconds = self.config.get("hide_cursor_seconds", 3)

        # Muat Halaman TV
        url = self.config.get("tv_url", "http://127.0.0.1:3154/tv/jadwal")
        print(f"Membuka URL Smart TV: {url}")
        self.browser.load(QUrl(url))

        # Event filter untuk keyboard & mouse
        QApplication.instance().installEventFilter(self)

        # Tampilkan Fullscreen
        if self.config.get("fullscreen", True):
            self.showFullScreen()
        else:
            self.resize(1280, 720)
            self.show()

        # Reset timer sembunyikan kursor
        self.reset_cursor_timer()

        # Daftarkan startup jika diset aktif
        if self.config.get("auto_startup", True) and getattr(sys, 'frozen', False):
            set_windows_startup(True)

    def reset_cursor_timer(self):
        QApplication.restoreOverrideCursor()
        if self.hide_cursor_seconds > 0:
            self.hide_cursor_timer.start(int(self.hide_cursor_seconds * 1000))

    def hide_cursor(self):
        QApplication.setOverrideCursor(Qt.CursorShape.BlankCursor)

    def eventFilter(self, obj, event):
        # Deteksi pergerakan mouse untuk memunculkan kursor sesaat
        if event.type() in (QEvent.Type.MouseMove, QEvent.Type.MouseButtonPress):
            self.reset_cursor_timer()

        # Filter Penekanan Tombol Keyboard
        if event.type() == QEvent.Type.KeyPress:
            key = event.key()
            modifiers = event.modifiers()
            has_ctrl = bool(modifiers & Qt.KeyboardModifier.ControlModifier)
            has_alt = bool(modifiers & Qt.KeyboardModifier.AltModifier)

            now = time.time()

            # 1. Deteksi Langkah 1: Ctrl + C
            if has_ctrl and key == Qt.Key.Key_C:
                self.ctrl_c_timestamp = now
                print(">> Secret Sequence [1/2]: Ctrl + C terdeteksi. Tekan B untuk menutup aplikasi...")
                return True  # Intercept agar tidak memicu copy biasa

            # 2. Deteksi Langkah 2: Tekan B dalam rentang 3 detik setelah Ctrl + C
            if key == Qt.Key.Key_B:
                if (now - self.ctrl_c_timestamp) <= 3.0:
                    print(">> Secret Sequence [2/2]: B terdeteksi! Kombinasi Ctrl + C + B valid. Menutup aplikasi Smart TV Kiosk.")
                    QApplication.restoreOverrideCursor()
                    QApplication.quit()
                    return True

            # 3. Blokir Tombol Penutup Standar (Esc, Alt+F4, Ctrl+W, Ctrl+Q, F11)
            if key == Qt.Key.Key_Escape:
                print(">> Tombol Escape diblokir untuk keamanan Kiosk.")
                return True
            if has_alt and key == Qt.Key.Key_F4:
                print(">> Tombol Alt+F4 diblokir untuk keamanan Kiosk.")
                return True
            if has_ctrl and key in (Qt.Key.Key_W, Qt.Key.Key_Q):
                print(">> Tombol Ctrl+W / Ctrl+Q diblokir.")
                return True
            if key == Qt.Key.Key_F11:
                return True

        return super().eventFilter(obj, event)

    def closeEvent(self, event):
        # Mencegah penutupan window tanpa melalui kombinasi tombol
        now = time.time()
        if (now - self.ctrl_c_timestamp) <= 3.0:
            event.accept()
        else:
            event.ignore()

def main():
    config = load_config()
    app = QApplication(sys.argv)
    app.setApplicationName("SmartTvKiosk")
    app.setOrganizationName("SMKTelkomLampung")

    window = KioskWindow(config)
    sys.exit(app.exec())

if __name__ == "__main__":
    main()
