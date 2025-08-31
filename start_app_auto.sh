#!/bin/bash

echo "========================================"
echo "Medical Booking System - Auto Start"
echo "========================================"
echo

echo "Starting Flask AI Service..."
cd nlp
python3 start_flask.py &
FLASK_PID=$!
cd ..

echo "Waiting for Flask service to start..."
sleep 10

echo "Starting Laravel Application..."
php artisan serve --host=127.0.0.1 --port=8000 &
LARAVEL_PID=$!

echo "Waiting for Laravel to start..."
sleep 5

echo
echo "========================================"
echo "Checking Service Status..."
echo "========================================"

# Check if we're on a system that supports PowerShell
if command -v pwsh &> /dev/null; then
    pwsh -ExecutionPolicy Bypass -File check_status.ps1
elif command -v powershell &> /dev/null; then
    powershell -ExecutionPolicy Bypass -File check_status.ps1
else
    echo "PowerShell not available, checking services manually..."

    # Check Laravel
    if curl -s http://127.0.0.1:8000 > /dev/null; then
        echo "✅ Laravel Application: RUNNING"
    else
        echo "❌ Laravel Application: NOT RUNNING"
    fi

    # Check Flask
    if curl -s http://127.0.0.1:5005/health > /dev/null; then
        echo "✅ Flask AI Service: RUNNING"
    else
        echo "❌ Flask AI Service: NOT RUNNING"
    fi
fi

echo
echo "========================================"
echo "Services Started Successfully!"
echo "========================================"
echo
echo "Access URLs:"
echo "Laravel App: http://127.0.0.1:8000"
echo "AI Assistant: http://127.0.0.1:8000/ai-assistant"
echo "Flask API: http://127.0.0.1:5005"
echo
echo "Press Enter to open the application..."
read

# Try to open the application in the default browser
if command -v xdg-open &> /dev/null; then
    xdg-open http://127.0.0.1:8000
elif command -v open &> /dev/null; then
    open http://127.0.0.1:8000
else
    echo "Please open http://127.0.0.1:8000 in your browser"
fi

echo
echo "Application opened in your browser!"
echo "Keep this terminal open to monitor the services."
echo "Press Ctrl+C to stop all services."
echo

# Wait for user to stop
wait
