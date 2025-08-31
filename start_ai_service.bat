@echo off
echo ========================================
echo Medical Booking System - AI Service
echo ========================================
echo.

cd nlp

echo Starting Flask AI Service...
echo.
echo This will:
echo 1. Install Python dependencies
echo 2. Start the Flask service on port 5005
echo.
echo Press Ctrl+C to stop the service
echo.

REM Try to use virtual environment if it exists
if exist "venv\Scripts\python.exe" (
    echo Using virtual environment...
    venv\Scripts\python.exe start_flask.py
) else (
    echo No virtual environment found, using system Python...
    python start_flask.py
)

pause
