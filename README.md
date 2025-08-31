# 🏥 Medical Booking System

A comprehensive Laravel-based medical appointment booking system with advanced AI integration, featuring role-based access for administrators, doctors, and patients.

## 🌟 Features

### 🤖 AI-Powered Features
- **AI Medical Assistant**: Intelligent chatbot for medical queries and symptom analysis
- **Smart Appointment Booking**: AI-driven appointment scheduling with intelligent slot recommendations
- **Medical Query Processing**: Natural language processing for medical questions
- **Symptom Analysis**: AI-powered symptom assessment and preliminary diagnosis
- **Doctor Recommendations**: Intelligent doctor matching based on symptoms and specialties
- **Medical Knowledge Base**: Comprehensive medical database integration
- **Conversation History**: Persistent AI conversation tracking for better user experience

### 👥 Multi-Role System
- **Admin Panel**: Complete system management and oversight
- **Doctor Dashboard**: Appointment management and patient care
- **Patient Portal**: Easy appointment booking and health tracking

### 📅 Appointment Management
- **Smart Scheduling**: AI-optimized appointment scheduling
- **Reschedule & Cancel**: Flexible appointment modifications
- **Follow-up Appointments**: Automated follow-up scheduling
- **Real-time Availability**: Live slot availability checking
- **Calendar Integration**: Seamless calendar management

### 💰 Wallet System
- **Digital Wallet**: Secure payment management
- **Transaction History**: Complete payment tracking
- **Multiple Payment Methods**: Credit cards, bank transfers
- **Refund Processing**: Automated refund handling
- **Payment Analytics**: Detailed financial reporting

### 🔐 Security & Authentication
- **Role-based Access Control**: Secure multi-role authentication
- **CSRF Protection**: Cross-site request forgery protection
- **Input Validation**: Comprehensive data validation
- **Secure Sessions**: Encrypted session management

## 🚀 Technology Stack

### Backend
- **Laravel 12**: Modern PHP framework
- **MySQL**: Reliable database management
- **Eloquent ORM**: Advanced database relationships
- **Laravel Sanctum**: API authentication

### Frontend
- **Blade Templates**: Server-side templating
- **Tailwind CSS**: Modern utility-first CSS
- **Alpine.js**: Lightweight JavaScript framework
- **Responsive Design**: Mobile-first approach

### AI Integration
- **Flask Python Service**: AI/ML processing backend
- **Natural Language Processing**: Medical text analysis
- **Machine Learning Models**: Symptom classification
- **RESTful API**: Seamless AI integration

## 📋 Prerequisites

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js & NPM
- Python 3.8+ (for AI features)

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone https://github.com/YusufJojeh/MedApp.git
cd MedApp
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
```bash
# Update .env with your database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medical_booking
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations
```bash
php artisan migrate
php artisan db:seed
```

### 6. Build Assets
```bash
npm run build
```

### 7. Start the Server
```bash
php artisan serve
```

## 🤖 AI Service Setup

### 1. Download AI Components
Download the AI components from the latest release:
- **GitHub Release**: [Download NLP AI Components](https://github.com/YusufJojeh/MedApp/releases/latest)
- Extract the `nlp.zip` file to your project root

### 2. Install Python Dependencies
```bash
cd nlp
pip install -r requirements.txt
```

### 3. Start AI Service
```bash
python server.py
```

### 4. Configure AI Integration
Update your `.env` file:
```env
AI_SERVICE_URL=http://localhost:5000
AI_ENABLED=true
```

## 📁 Project Structure

```
medical-booking/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   ├── Doctor/         # Doctor controllers
│   │   ├── Patient/        # Patient controllers
│   │   └── Auth/           # Authentication
│   ├── Models/             # Eloquent models
│   └── Services/           # Business logic
├── resources/views/
│   ├── admin/              # Admin views
│   ├── doctor/             # Doctor views
│   ├── patient/            # Patient views
│   └── layouts/            # Shared layouts
├── routes/
│   ├── web.php             # Web routes
│   └── api.php             # API routes
├── database/
│   └── migrations/         # Database migrations
└── nlp/                    # AI/ML components
```

## 🔧 Configuration

### Environment Variables
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medical_booking
DB_USERNAME=your_username
DB_PASSWORD=your_password

# AI Service
AI_SERVICE_URL=http://localhost:5000
AI_ENABLED=true

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

## 🎯 Key Features Explained

### AI Medical Assistant
The system includes an intelligent AI assistant that can:
- Answer medical questions using a comprehensive knowledge base
- Analyze symptoms and provide preliminary assessments
- Recommend appropriate doctors based on medical needs
- Assist with appointment booking through natural conversation
- Maintain conversation history for personalized experience

### Smart Appointment Booking
- **Intelligent Slot Selection**: AI recommends optimal appointment times
- **Doctor Matching**: Suggests the best doctor based on symptoms
- **Conflict Detection**: Automatically detects scheduling conflicts
- **Follow-up Scheduling**: Suggests follow-up appointments based on treatment plans

### Multi-Role Dashboard
- **Admin**: System management, user oversight, analytics
- **Doctor**: Patient management, appointment scheduling, medical records
- **Patient**: Easy booking, health tracking, payment management

## 🔒 Security Features

- **Role-based Authentication**: Secure access control
- **CSRF Protection**: Cross-site request forgery prevention
- **Input Sanitization**: XSS protection
- **SQL Injection Prevention**: Parameterized queries
- **Session Security**: Encrypted session data

## 📊 Database Schema

### Core Tables
- `users` - User accounts and authentication
- `doctors` - Doctor profiles and specialties
- `patients` - Patient information and medical history
- `appointments` - Appointment scheduling and management
- `specialties` - Medical specialties
- `wallets` - Payment and transaction management
- `working_hours` - Doctor availability schedules

## 🚀 Deployment

### Production Setup
1. **Server Requirements**: PHP 8.2+, MySQL 8.0+, Nginx/Apache
2. **SSL Certificate**: HTTPS for secure data transmission
3. **Environment**: Production-optimized settings
4. **Monitoring**: Application performance monitoring
5. **Backup**: Regular database and file backups

### Docker Deployment
```bash
# Build and run with Docker
docker-compose up -d
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
- **Email**: support@medapp.com
- **Documentation**: [Wiki](https://github.com/YusufJojeh/MedApp/wiki)
- **Issues**: [GitHub Issues](https://github.com/YusufJojeh/MedApp/issues)

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- Alpine.js
- Flask Python Framework
- Medical Knowledge Databases

---

**Built with ❤️ for better healthcare management**
