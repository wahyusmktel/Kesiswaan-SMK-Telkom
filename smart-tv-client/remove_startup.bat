@echo off
reg delete "HKCU\Software\Microsoft\Windows\CurrentVersion\Run" /v SmartTvKiosk /f
echo Smart TV Kiosk telah dihapus dari Windows Startup!
pause
