@echo off
title Cepatik Cloud Printing
echo   MENJALANKAN SERVER LOKAL CEPATIK CLOUD PRINTING KIOSK
echo Membuka Portal Navigasi Cepatik di browser...
echo Alamat: http://localhost:8000
echo.
echo Tekan Ctrl + C di jendela ini jika ingin mematikan server.
echo.

start http://localhost:8000

if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" -S localhost:8000
) else (
    php -S localhost:8000
)

pause

