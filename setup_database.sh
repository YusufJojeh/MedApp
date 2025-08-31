#!/bin/bash

# Medical Booking System - Database Setup Script
# This script will set up the complete database for the Medical Booking System

echo "🏥 Medical Booking System - Database Setup"
echo "=========================================="

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    echo "❌ Error: This script must be run from the root of a Laravel project"
    exit 1
fi

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "❌ Error: .env file not found. Please create one from .env.example"
    exit 1
fi

echo "📋 Checking prerequisites..."

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Error: Composer is not installed"
    exit 1
fi

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP is not installed"
    exit 1
fi

echo "✅ Prerequisites check passed"

# Clear caches
echo "🧹 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "✅ Caches cleared"

# Install dependencies if needed
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install
    echo "✅ Dependencies installed"
else
    echo "✅ Dependencies already installed"
fi

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

if [ $? -eq 0 ]; then
    echo "✅ Migrations completed successfully"
else
    echo "❌ Error: Migrations failed"
    exit 1
fi

# Run seeders
echo "🌱 Seeding database with sample data..."
php artisan db:seed --force

if [ $? -eq 0 ]; then
    echo "✅ Database seeded successfully"
else
    echo "❌ Error: Seeding failed"
    exit 1
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
    echo "✅ Application key generated"
else
    echo "✅ Application key already set"
fi

echo ""
echo "🎉 Database setup completed successfully!"
echo ""
echo "📊 Database Summary:"
echo "   - 14 tables created"
echo "   - 14 users created (2 admins, 6 doctors, 6 patients)"
echo "   - 10 medical specialties"
echo "   - 6 doctors with working hours"
echo "   - 6 patients with medical profiles"
echo "   - 6 sample appointments"
echo "   - 4 subscription plans with features"
echo "   - Sample payments and wallets"
echo ""
echo "🔐 Default login credentials:"
echo "   All users have password: password"
echo ""
echo "👨‍⚕️  Sample Doctor Login:"
echo "   Email: ahmed.ali@medical.com"
echo "   Username: dr_ahmed"
echo ""
echo "👤 Sample Patient Login:"
echo "   Email: ahmed@email.com"
echo "   Username: patient_ahmed"
echo ""
echo "🔧 Next steps:"
echo "   1. Create Laravel Models for each table"
echo "   2. Set up Eloquent relationships"
echo "   3. Create Controllers for API endpoints"
echo "   4. Implement authentication and authorization"
echo "   5. Build the frontend application"
echo ""
echo "📖 For more information, see DATABASE_SETUP.md"
