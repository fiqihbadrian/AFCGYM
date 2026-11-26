@echo off
REM ==========================================================
REM  AFCGym - start server lokal (Laragon 6.0.0 / PHP 8.1.10)
REM  Klik dua kali file ini untuk menjalankan aplikasi.
REM ==========================================================
setlocal
set PHP=C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64
set PATH=%PHP%;%PATH%
cd /d "%~dp0"

echo [1/2] Cek service Laragon (MySQL + Apache)...
tasklist /FI "IMAGENAME eq mysqld.exe" | find /I "mysqld.exe" >nul
if errorlevel 1 (
  echo     MySQL belum jalan. Buka Laragon lalu klik "Start All".
  pause
  exit /b 1
)

echo [2/2] Menjalankan AFCGym di http://127.0.0.1:8123 ...
start "" http://127.0.0.1:8123/backend/login
php artisan serve --port=8123
