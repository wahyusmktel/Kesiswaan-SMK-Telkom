@echo off
set EXE_PATH=%~dp0SmartTvKiosk.exe
reg add "HKCU\Software\Microsoft\Windows\CurrentVersion\Run" /v SmartTvKiosk /t REG_SZ /d "\"%EXE_PATH%\"" /f
echo Smart TV Kiosk telah ditambahkan ke Windows Startup!
pause
