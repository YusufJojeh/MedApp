# 🏥 Medical Booking System

A comprehensive Laravel-based medical appointment booking system with role-based access control for patients, doctors, and administrators.

![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC.svg)

## ✨ Features

### 👥 Multi-Role System
- **Patients**: Book appointments, manage profile, view medical history
- **Doctors**: Manage appointments, patient records, working hours
- **Administrators**: Full system management and oversight

### 🗓️ Appointment Management
- Real-time appointment booking
- Doctor availability checking
- Appointment rescheduling and cancellation
- Follow-up appointment scheduling
- Working hours management

### 💰 Payment & Wallet System
- Integrated wallet system for patients
- Payment method management
- Transaction history
- Fund management (add/withdraw)

### 🎨 Modern UI/UX
- Responsive design with Tailwind CSS
- Dark/Light mode support
- Interactive dashboards
- Real-time notifications

### 🔐 Security Features
- Role-based authentication
- CSRF protection
- Input validation
- Secure payment processing

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js & NPM (for frontend assets)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/YusufJojeh/MedApp.git
   cd MedApp
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Edit `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=medical_booking
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed the database (optional)**
   ```bash
   php artisan db:seed
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

## 📁 Project Structure

```
medical-booking/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin controllers
│   │   │   ├── Doctor/         # Doctor controllers
│   │   │   ├── Patient/        # Patient controllers
│   │   │   └── Auth/           # Authentication controllers
│   │   └── Middleware/         # Custom middleware
│   ├── Models/                 # Eloquent models
│   └── Services/               # Business logic services
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── resources/
│   ├── views/
│   │   ├── admin/              # Admin views
│   │   ├── doctor/             # Doctor views
│   │   ├── patient/            # Patient views
│   │   └── layouts/            # Layout templates
│   ├── css/                    # Stylesheets
│   └── js/                     # JavaScript files
├── routes/
│   ├── web.php                 # Web routes
│   └── api.php                 # API routes
└── public/                     # Public assets
```

## 🎯 Key Features Breakdown

### Patient Features
- **Dashboard**: Overview of appointments, medical history, and wallet
- **Appointment Booking**: Search doctors, check availability, book appointments
- **Profile Management**: Update personal information and medical history
- **Wallet System**: Manage funds, payment methods, and transactions
- **Doctor Favorites**: Save and manage favorite doctors

### Doctor Features
- **Dashboard**: Overview of appointments, patients, and earnings
- **Appointment Management**: View, accept, and manage appointments
- **Patient Records**: Access patient medical history and notes
- **Working Hours**: Set and manage availability
- **Profile Management**: Update professional information

### Admin Features
- **Dashboard**: System overview with statistics and analytics
- **User Management**: Manage patients, doctors, and staff
- **Appointment Oversight**: Monitor and manage all appointments
- **Payment Management**: Track transactions and manage refunds
- **System Settings**: Configure system-wide settings

## 🔧 Configuration

### Database Configuration
The system uses MySQL with the following main tables:
- `users` - User accounts and authentication
- `patients` - Patient-specific information
- `doctors` - Doctor profiles and specialties
- `appointments` - Appointment records
- `wallets` - User wallet balances
- `working_hours` - Doctor availability schedules

### Environment Variables
Key environment variables to configure:
```env
APP_NAME="Medical Booking System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medical_booking
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

## 🚀 Deployment

### Production Deployment
1. Set up a production server with PHP 8.2+, MySQL, and Nginx/Apache
2. Clone the repository to your server
3. Install dependencies: `composer install --optimize-autoloader --no-dev`
4. Configure environment variables for production
5. Run migrations: `php artisan migrate --force`
6. Set up proper file permissions
7. Configure web server to point to the `public` directory
8. Set up SSL certificate for HTTPS

### Docker Deployment (Optional)
```dockerfile
FROM php:8.2-fpm
# Add your Dockerfile configuration
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue on GitHub
- Email: support@medicalbooking.com
- Documentation: [Wiki](https://github.com/YusufJojeh/MedApp/wiki)

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- Font Awesome Icons
- Alpine.js for interactive components

---

**Built with ❤️ using Laravel**
