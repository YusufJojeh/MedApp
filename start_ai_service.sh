#!/bin/bash

echo "========================================"
echo "Medical Booking System - AI Service"
echo "========================================"
echo

cd nlp

echo "Starting Flask AI Service..."
echo
echo "This will:"
echo "1. Install Python dependencies"
echo "2. Start the Flask service on port 5005"
echo
echo "Press Ctrl+C to stop the service"
echo

# Try to use virtual environment if it exists
if [ -f "nlp/venv/bin/python" ]; then
    echo "Using virtual environment..."
    nlp/venv/bin/python nlp/start_flask.py
else
    echo "No virtual environment found, using system Python..."
    python3 nlp/start_flask.py
fi
