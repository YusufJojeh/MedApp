# Medical Booking System - Migration Summary

This document provides a complete overview of all the database migrations and seeders created for the Medical Booking System.

## 📁 Files Created

### Database Migrations (15 files)

1. **`database/migrations/2024_01_01_000000_update_users_table.php`**
   - Updates the default Laravel users table
   - Adds: username, role, first_name, last_name, phone, status, profile_image, last_login
   - Removes: name, email_verified_at, remember_token

2. **`database/migrations/2024_01_01_000001_create_specialties_table.php`**
   - Creates medical specialties table
   - Fields: name_en, name_ar, description, icon

3. **`database/migrations/2024_01_01_000002_create_specialty_pricing_table.php`**
   - Creates specialty pricing table
   - Fields: specialty_id, base_price, currency, note
   - Foreign key to specialties table

4. **`database/migrations/2024_01_01_000003_create_doctors_table.php`**
   - Creates doctors table
   - Fields: user_id, name, specialty_id, description, experience_years, education, languages, consultation_fee, is_active, is_featured, rating, total_reviews
   - Foreign keys to users and specialties tables

5. **`database/migrations/2024_01_01_000004_create_doctor_pricing_overrides_table.php`**
   - Creates doctor pricing overrides table
   - Fields: doctor_id, override_price, currency, note
   - Foreign key to doctors table

6. **`database/migrations/2024_01_01_000005_create_working_hours_table.php`**
   - Creates working hours table
   - Fields: doctor_id, day_of_week, start_time, end_time, is_available
   - Foreign key to doctors table

7. **`database/migrations/2024_01_01_000006_create_patients_table.php`**
   - Creates patients table
   - Fields: user_id, NAME, phone, email, date_of_birth, gender, blood_type, address, medical_history, emergency_contact, status
   - Foreign key to users table

8. **`database/migrations/2024_01_01_000007_create_appointments_table.php`**
   - Creates appointments table
   - Fields: patient_id, doctor_id, appointment_date, appointment_time, STATUS, notes
   - Foreign keys to patients and doctors tables

9. **`database/migrations/2024_01_01_000008_create_plans_table.php`**
   - Creates subscription plans table
   - Fields: name, slug, audience, price, currency, billing_cycle, is_popular, sort_order

10. **`database/migrations/2024_01_01_000009_create_plan_features_table.php`**
    - Creates plan features table
    - Fields: plan_id, label, is_included, note, sort_order
    - Foreign key to plans table

11. **`database/migrations/2024_01_01_000010_create_subscriptions_table.php`**
    - Creates subscriptions table
    - Fields: user_id, plan_id, STATUS, renews_at, canceled_at, provider, provider_subscription_id, meta
    - Foreign keys to users and plans tables

12. **`database/migrations/2024_01_01_000011_create_payments_table.php`**
    - Creates payments table
    - Fields: user_id, doctor_id, appointment_id, provider, provider_payment_id, STATUS, amount, currency, platform_fee, net_amount, meta
    - Foreign keys to users, doctors, and appointments tables

13. **`database/migrations/2024_01_01_000012_create_payment_webhooks_table.php`**
    - Creates payment webhooks table
    - Fields: provider, event_type, event_id, payment_id, payload, processed, error_message
    - Foreign key to payments table

14. **`database/migrations/2024_01_01_000013_create_wallets_table.php`**
    - Creates wallets table
    - Fields: user_id, balance, currency
    - Foreign key to users table

15. **`database/migrations/2024_01_01_000014_create_wallet_transactions_table.php`**
    - Creates wallet transactions table
    - Fields: wallet_id, payment_id, TYPE, amount, reason, balance_before, balance_after, meta
    - Foreign keys to wallets and payments tables

### Database Seeders (13 files)

1. **`database/seeders/SpecialtySeeder.php`**
   - Seeds 10 medical specialties
   - Includes: Cardiology, Neurology, Ophthalmology, Dentistry, Pediatrics, Obstetrics & Gynecology, Dermatology, Orthopedics, Internal Medicine, Psychiatry

2. **`database/seeders/SpecialtyPricingSeeder.php`**
   - Seeds base pricing for all specialties
   - Prices range from 80 SAR to 160 SAR

3. **`database/seeders/UserSeeder.php`**
   - Seeds 14 users (2 admins, 6 doctors, 6 patients)
   - All users have password: "password"

4. **`database/seeders/DoctorSeeder.php`**
   - Seeds 6 doctors with different specialties
   - Includes experience, education, languages, consultation fees

5. **`database/seeders/DoctorPricingOverrideSeeder.php`**
   - Seeds custom pricing for 3 doctors
   - Overrides base specialty pricing

6. **`database/seeders/WorkingHoursSeeder.php`**
   - Seeds working hours for all 6 doctors
   - Sunday to Thursday schedules with different time slots

7. **`database/seeders/PatientSeeder.php`**
   - Seeds 6 patients with complete medical profiles
   - Includes medical history, emergency contacts, blood types

8. **`database/seeders/PlanSeeder.php`**
   - Seeds 4 subscription plans
   - Starter, Professional, Premium, Enterprise

9. **`database/seeders/PlanFeatureSeeder.php`**
   - Seeds 32 plan features across all 4 plans
   - Includes included/excluded features with upgrade notes

10. **`database/seeders/SubscriptionSeeder.php`**
    - Seeds 3 active subscriptions for doctors
    - All on Professional plan

11. **`database/seeders/AppointmentSeeder.php`**
    - Seeds 6 sample appointments
    - Mix of scheduled, confirmed, and completed statuses

12. **`database/seeders/WalletSeeder.php`**
    - Seeds wallets for all 6 doctors
    - Initial balance of 0.00 SAR

13. **`database/seeders/PaymentSeeder.php`**
    - Seeds 1 sample payment transaction
    - Successful Stripe payment for first appointment

### Updated Files

1. **`database/seeders/DatabaseSeeder.php`**
   - Updated to call all seeders in correct order

### Documentation Files

1. **`DATABASE_SETUP.md`**
   - Comprehensive setup guide
   - Includes troubleshooting and next steps

2. **`MIGRATION_SUMMARY.md`** (this file)
   - Complete overview of all created files

### Automation Scripts

1. **`setup_database.sh`**
   - Linux/macOS automation script
   - Checks prerequisites, runs migrations and seeders

2. **`setup_database.bat`**
   - Windows automation script
   - Same functionality as shell script but for Windows

## 🗄️ Database Schema Summary

### Core Tables (5)
- `users` - User accounts with roles
- `specialties` - Medical specialties
- `doctors` - Doctor profiles
- `patients` - Patient profiles
- `appointments` - Booking appointments

### Pricing & Billing Tables (7)
- `specialty_pricing` - Base specialty pricing
- `doctor_pricing_overrides` - Custom doctor pricing
- `plans` - Subscription plans
- `plan_features` - Plan features
- `subscriptions` - User subscriptions
- `payments` - Payment transactions
- `payment_webhooks` - Payment webhook events

### Financial Tables (2)
- `wallets` - User wallet balances
- `wallet_transactions` - Wallet transaction history

### Scheduling Tables (1)
- `working_hours` - Doctor availability schedules

## 📊 Sample Data Summary

### Users Created
- **2 Admin users**: admin, supervisor
- **6 Doctor users**: dr_ahmed, dr_fatma, dr_mohamed, dr_sara, dr_ali, dr_nora
- **6 Patient users**: patient_ahmed, patient_sara, patient_mohamed, patient_fatma, patient_ali, patient_nora

### Medical Data
- **10 Medical specialties** with Arabic and English names
- **6 Doctors** with different specialties and experience levels
- **6 Patients** with complete medical profiles
- **6 Appointments** with various statuses

### Business Data
- **4 Subscription plans** with complete feature sets
- **3 Active subscriptions** for doctors
- **1 Sample payment** transaction
- **6 Doctor wallets** with zero initial balance

## 🔐 Default Credentials

All users are created with the password: `password`

### Sample Logins
- **Admin**: admin@medical.com / admin
- **Doctor**: ahmed.ali@medical.com / dr_ahmed
- **Patient**: ahmed@email.com / patient_ahmed

## 🚀 Quick Start

### For Windows Users
```bash
setup_database.bat
```

### For Linux/macOS Users
```bash
chmod +x setup_database.sh
./setup_database.sh
```

### Manual Setup
```bash
php artisan migrate
php artisan db:seed
```

## 📋 Next Steps

After running the migrations and seeders:

1. **Create Laravel Models** for each table
2. **Set up Eloquent relationships** between models
3. **Create Controllers** for API endpoints
4. **Implement authentication** and authorization
5. **Build the frontend** application

## 🔧 Technical Details

- **Database**: MySQL/MariaDB with utf8mb4 character set
- **Framework**: Laravel 10+
- **PHP Version**: 8.1+
- **Foreign Keys**: All properly configured with cascade/set null rules
- **Indexes**: Optimized for common queries
- **JSON Fields**: Used for meta data storage
- **Timestamps**: All tables include created_at/updated_at where appropriate

## 📞 Support

For issues or questions:
1. Check `DATABASE_SETUP.md` for troubleshooting
2. Review Laravel logs: `storage/logs/laravel.log`
3. Verify database connection settings in `.env`
4. Ensure all prerequisites are installed
