#!/usr/bin/env python3
"""
Flask AI Service Startup Script
This script ensures all dependencies are installed and starts the Flask service.
"""

import subprocess
import sys
import os
from pathlib import Path

def install_requirements():
    """Install required Python packages"""
    print("📦 Installing Python dependencies...")
    try:
        subprocess.check_call([sys.executable, "-m", "pip", "install", "-r", "requirements.txt"])
        print("✅ Dependencies installed successfully")
        return True
    except subprocess.CalledProcessError as e:
        print(f"❌ Failed to install dependencies: {e}")
        return False

def check_flask_service():
    """Check if Flask service is already running"""
    try:
        import requests
        response = requests.get("http://127.0.0.1:5005/health", timeout=2)
        if response.status_code == 200:
            print("✅ Flask service is already running on port 5005")
            return True
    except:
        pass
    return False

def start_flask_service():
    """Start the Flask service"""
    print("🚀 Starting Flask AI Service on port 5005...")
    try:
        # Change to the nlp directory
        os.chdir(Path(__file__).parent)

        # Check if virtual environment exists
        venv_path = Path("venv")
        if venv_path.exists():
            print("📦 Using existing virtual environment...")
            # On Windows, activate the virtual environment
            if os.name == 'nt':  # Windows
                python_path = venv_path / "Scripts" / "python.exe"
            else:  # Unix/Linux/Mac
                python_path = venv_path / "bin" / "python"

            if python_path.exists():
                subprocess.run([str(python_path), "server.py"])
            else:
                print("❌ Virtual environment Python not found, trying system Python...")
                subprocess.run([sys.executable, "server.py"])
        else:
            print("📦 No virtual environment found, using system Python...")
            subprocess.run([sys.executable, "server.py"])
    except KeyboardInterrupt:
        print("\n🛑 Flask service stopped by user")
    except Exception as e:
        print(f"❌ Failed to start Flask service: {e}")

def main():
    print("=" * 60)
    print("🤖 Medical Booking System - Flask AI Service")
    print("=" * 60)

    # Check if already running
    if check_flask_service():
        print("Service is already running. Use Ctrl+C to stop.")
        return

    # Install dependencies
    if not install_requirements():
        print("Failed to install dependencies. Please check your Python environment.")
        return

    # Start service
    start_flask_service()

if __name__ == "__main__":
    main()
