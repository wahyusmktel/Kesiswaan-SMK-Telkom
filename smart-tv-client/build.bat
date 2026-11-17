@echo off
echo ========================================================
echo   Building Smart TV Kiosk Desktop App (.EXE)
echo ========================================================

cd /d "%~dp0"
pip install pyqt6 pyqt6-webengine pyinstaller
pyinstaller --noconsole --onefile --clean --name "SmartTvKiosk" main.py

copy config.json dist\config.json
echo ========================================================
echo   BUILD SELESAI!
echo   File siap dijalankan di folder dist\SmartTvKiosk.exe
echo ========================================================
pause
