# Medical Booking System - Database Setup Guide

This guide will help you set up the complete database for the Medical Booking System using Laravel migrations and seeders.

## Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL/MariaDB database server
- Laravel project set up

## Database Configuration

1. **Configure your database connection** in `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medical_booking
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

2. **Create the database**:

```sql
CREATE DATABASE medical_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Running Migrations

The migrations are designed to be run in the correct order to handle foreign key dependencies. Run the following commands:

```bash
# Clear any existing migrations cache
php artisan config:clear
php artisan cache:clear

# Run all migrations
php artisan migrate
```

### Migration Order

The migrations will run in this order:

1. `2024_01_01_000000_update_users_table.php` - Updates the users table structure
2. `2024_01_01_000001_create_specialties_table.php` - Creates medical specialties
3. `2024_01_01_000002_create_specialty_pricing_table.php` - Creates specialty pricing
4. `2024_01_01_000003_create_doctors_table.php` - Creates doctors table
5. `2024_01_01_000004_create_doctor_pricing_overrides_table.php` - Creates doctor pricing overrides
6. `2024_01_01_000005_create_working_hours_table.php` - Creates working hours
7. `2024_01_01_000006_create_patients_table.php` - Creates patients table
8. `2024_01_01_000007_create_appointments_table.php` - Creates appointments table
9. `2024_01_01_000008_create_plans_table.php` - Creates subscription plans
10. `2024_01_01_000009_create_plan_features_table.php` - Creates plan features
11. `2024_01_01_000010_create_subscriptions_table.php` - Creates subscriptions
12. `2024_01_01_000011_create_payments_table.php` - Creates payments table
13. `2024_01_01_000012_create_payment_webhooks_table.php` - Creates payment webhooks
14. `2024_01_01_000013_create_wallets_table.php` - Creates wallets
15. `2024_01_01_000014_create_wallet_transactions_table.php` - Creates wallet transactions

## Seeding the Database

After running migrations, seed the database with sample data:

```bash
# Run all seeders
php artisan db:seed
```

### Seeder Order

The seeders will run in this order:

1. `SpecialtySeeder` - Seeds medical specialties
2. `SpecialtyPricingSeeder` - Seeds specialty pricing
3. `UserSeeder` - Seeds users (admins, doctors, patients)
4. `DoctorSeeder` - Seeds doctors
5. `DoctorPricingOverrideSeeder` - Seeds doctor pricing overrides
6. `WorkingHoursSeeder` - Seeds working hours
7. `PatientSeeder` - Seeds patients
8. `PlanSeeder` - Seeds subscription plans
9. `PlanFeatureSeeder` - Seeds plan features
10. `SubscriptionSeeder` - Seeds subscriptions
11. `AppointmentSeeder` - Seeds appointments
12. `WalletSeeder` - Seeds wallets
13. `PaymentSeeder` - Seeds payments

## Database Schema Overview

### Core Tables

- **users** - User accounts with roles (admin, doctor, patient)
- **specialties** - Medical specialties (Cardiology, Neurology, etc.)
- **doctors** - Doctor profiles linked to users and specialties
- **patients** - Patient profiles linked to users
- **appointments** - Booking appointments between patients and doctors

### Pricing & Billing

- **specialty_pricing** - Base pricing for each specialty
- **doctor_pricing_overrides** - Custom pricing for individual doctors
- **plans** - Subscription plans for doctors
- **plan_features** - Features included in each plan
- **subscriptions** - User subscriptions to plans
- **payments** - Payment transactions for appointments
- **payment_webhooks** - Webhook events from payment providers

### Financial Management

- **wallets** - User wallet balances
- **wallet_transactions** - All wallet transactions

### Scheduling

- **working_hours** - Doctor availability schedules

## Sample Data

The seeders will create:

### Users
- 2 Admin users (admin, supervisor)
- 6 Doctor users (dr_ahmed, dr_fatma, dr_mohamed, dr_sara, dr_ali, dr_nora)
- 6 Patient users (patient_ahmed, patient_sara, patient_mohamed, patient_fatma, patient_ali, patient_nora)

### Medical Specialties
- Cardiology, Neurology, Ophthalmology, Dentistry, Pediatrics
- Obstetrics & Gynecology, Dermatology, Orthopedics, Internal Medicine, Psychiatry

### Doctors
- 6 doctors with different specialties and experience levels
- Working hours for each doctor (Sunday to Thursday)
- Pricing overrides for some doctors

### Patients
- 6 patients with complete medical profiles
- Various medical histories and emergency contacts

### Appointments
- 6 sample appointments with different statuses
- Mix of scheduled, confirmed, and completed appointments

### Subscription Plans
- 4 plans: Starter, Professional, Premium, Enterprise
- Complete feature sets for each plan
- Sample subscriptions for doctors

### Payments
- 1 sample payment transaction
- Wallet setup for doctors

## Default Login Credentials

All users are created with the password: `password`

### Admin Users
- Email: `admin@medical.com` / Username: `admin`
- Email: `supervisor@medical.com` / Username: `supervisor`

### Doctor Users
- Email: `ahmed.ali@medical.com` / Username: `dr_ahmed`
- Email: `fatma.hassan@medical.com` / Username: `dr_fatma`
- Email: `mohamed.rahman@medical.com` / Username: `dr_mohamed`
- Email: `sara.mahmoud@medical.com` / Username: `dr_sara`
- Email: `ali.hassan@medical.com` / Username: `dr_ali`
- Email: `nora.ahmed@medical.com` / Username: `dr_nora`

### Patient Users
- Email: `ahmed@email.com` / Username: `patient_ahmed`
- Email: `sara@email.com` / Username: `patient_sara`
- Email: `mohamed@email.com` / Username: `patient_mohamed`
- Email: `fatma@email.com` / Username: `patient_fatma`
- Email: `ali@email.com` / Username: `patient_ali`
- Email: `nora@email.com` / Username: `patient_nora`

## Troubleshooting

### Common Issues

1. **Foreign Key Constraints**: Make sure to run migrations in order
2. **Character Set**: Ensure your database uses `utf8mb4` character set
3. **Memory Issues**: If you encounter memory issues during seeding, increase PHP memory limit

### Reset Database

To completely reset the database:

```bash
# Drop all tables and re-run migrations
php artisan migrate:fresh

# Seed the database
php artisan db:seed
```

### Individual Seeders

To run individual seeders:

```bash
# Run specific seeder
php artisan db:seed --class=UserSeeder

# Run multiple specific seeders
php artisan db:seed --class=SpecialtySeeder
php artisan db:seed --class=UserSeeder
```

## Database Relationships

### Key Foreign Keys

- `doctors.user_id` → `users.id`
- `doctors.specialty_id` → `specialties.id`
- `patients.user_id` → `users.id`
- `appointments.patient_id` → `patients.id`
- `appointments.doctor_id` → `doctors.id`
- `payments.user_id` → `users.id`
- `payments.doctor_id` → `doctors.id`
- `payments.appointment_id` → `appointments.id`
- `subscriptions.user_id` → `users.id`
- `subscriptions.plan_id` → `plans.id`
- `wallets.user_id` → `users.id`

## Next Steps

After setting up the database:

1. Create Laravel Models for each table
2. Set up Eloquent relationships
3. Create Controllers for API endpoints
4. Implement authentication and authorization
5. Build the frontend application

## Support

If you encounter any issues during setup, please check:

1. Laravel logs: `storage/logs/laravel.log`
2. Database connection settings
3. PHP version compatibility
4. Required PHP extensions
