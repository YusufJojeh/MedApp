@echo off
echo ========================================
echo Medical Booking System - Auto Start
echo ========================================
echo.

echo Starting Flask AI Service...
start "Flask AI Service" cmd /k "cd nlp && python start_flask.py"

echo Waiting for Flask service to start...
timeout /t 10 /nobreak >nul

echo Starting Laravel Application...
start "Laravel App" cmd /k "php artisan serve --host=127.0.0.1 --port=8000"

echo Waiting for Laravel to start...
timeout /t 5 /nobreak >nul

echo.
echo ========================================
echo Checking Service Status...
echo ========================================

powershell -ExecutionPolicy Bypass -File check_status.ps1

echo.
echo ========================================
echo Services Started Successfully!
echo ========================================
echo.
echo Access URLs:
echo Laravel App: http://127.0.0.1:8000
echo AI Assistant: http://127.0.0.1:8000/ai-assistant
echo Flask API: http://127.0.0.1:5005
echo.
echo Press any key to open the application...
pause >nul

start http://127.0.0.1:8000

echo.
echo Application opened in your browser!
echo Keep this window open to monitor the services.
echo Press Ctrl+C in the service windows to stop them.
echo.
pause
