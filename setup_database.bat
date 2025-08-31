@echo off
REM Medical Booking System - Database Setup Script for Windows
REM This script will set up the complete database for the Medical Booking System

echo 🏥 Medical Booking System - Database Setup
echo ==========================================

REM Check if we're in a Laravel project
if not exist "artisan" (
    echo ❌ Error: This script must be run from the root of a Laravel project
    pause
    exit /b 1
)

REM Check if .env file exists
if not exist ".env" (
    echo ❌ Error: .env file not found. Please create one from .env.example
    pause
    exit /b 1
)

echo 📋 Checking prerequisites...

REM Check if composer is installed
composer --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Error: Composer is not installed or not in PATH
    pause
    exit /b 1
)

REM Check if PHP is installed
php --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Error: PHP is not installed or not in PATH
    pause
    exit /b 1
)

echo ✅ Prerequisites check passed

REM Clear caches
echo 🧹 Clearing Laravel caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo ✅ Caches cleared

REM Install dependencies if needed
if not exist "vendor" (
    echo 📦 Installing Composer dependencies...
    composer install
    echo ✅ Dependencies installed
) else (
    echo ✅ Dependencies already installed
)

REM Run migrations
echo 🗄️  Running database migrations...
php artisan migrate --force

if errorlevel 1 (
    echo ❌ Error: Migrations failed
    pause
    exit /b 1
) else (
    echo ✅ Migrations completed successfully
)

REM Run seeders
echo 🌱 Seeding database with sample data...
php artisan db:seed --force

if errorlevel 1 (
    echo ❌ Error: Seeding failed
    pause
    exit /b 1
) else (
    echo ✅ Database seeded successfully
)

REM Generate application key if not set
findstr /C:"APP_KEY=base64:" .env >nul 2>&1
if errorlevel 1 (
    echo 🔑 Generating application key...
    php artisan key:generate
    echo ✅ Application key generated
) else (
    echo ✅ Application key already set
)

echo.
echo 🎉 Database setup completed successfully!
echo.
echo 📊 Database Summary:
echo    - 14 tables created
echo    - 14 users created (2 admins, 6 doctors, 6 patients)
echo    - 10 medical specialties
echo    - 6 doctors with working hours
echo    - 6 patients with medical profiles
echo    - 6 sample appointments
echo    - 4 subscription plans with features
echo    - Sample payments and wallets
echo.
echo 🔐 Default login credentials:
echo    All users have password: password
echo.
echo 👨‍⚕️  Sample Doctor Login:
echo    Email: ahmed.ali@medical.com
echo    Username: dr_ahmed
echo.
echo 👤 Sample Patient Login:
echo    Email: ahmed@email.com
echo    Username: patient_ahmed
echo.
echo 🔧 Next steps:
echo    1. Create Laravel Models for each table
echo    2. Set up Eloquent relationships
echo    3. Create Controllers for API endpoints
echo    4. Implement authentication and authorization
echo    5. Build the frontend application
echo.
echo 📖 For more information, see DATABASE_SETUP.md
echo.
pause
