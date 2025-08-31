# 🚀 Medical Booking System - Complete Routing Documentation

## 📋 Overview

This document provides a comprehensive overview of all routes in the Medical Booking System created by **Hawraa Ahmad Balwi**. The system includes web routes, API routes, and proper middleware protection for different user roles.

## 🏗️ Route Structure

### Web Routes (`routes/web.php`)
- **Public Routes**: Homepage, about, services, contact
- **Authentication Routes**: Login, register, password reset
- **Protected Routes**: Role-based access for admin, doctor, and patient
- **AI Assistant Routes**: AI-powered features
- **General Routes**: Appointments, doctors, payments

### API Routes (`routes/api.php`)
- **Public API**: Health checks, public data
- **Authentication API**: Login, register, token management
- **Role-based API**: Admin, doctor, and patient endpoints
- **Webhook Routes**: Payment processing webhooks

## 🔐 Middleware Protection

### Custom Middleware Created
1. **AdminMiddleware** - Protects admin-only routes
2. **DoctorMiddleware** - Protects doctor-only routes  
3. **PatientMiddleware** - Protects patient-only routes
4. **AiServiceHealthCheck** - Monitors AI service health

## 📡 Complete Route Listing

### 🌐 Public Web Routes

#### Home & Public Pages
```
GET  /                    → HomeController@index
GET  /about              → HomeController@about
GET  /services           → HomeController@services
GET  /contact            → HomeController@contact
POST /contact            → HomeController@contactSubmit
GET  /faq                → HomeController@faq
GET  /privacy            → HomeController@privacy
GET  /terms              → HomeController@terms
GET  /search             → HomeController@search
GET  /specialties        → HomeController@getSpecialties
GET  /featured-doctors   → HomeController@getFeaturedDoctors
GET  /sitemap.xml        → HomeController@sitemap
GET  /robots.txt         → HomeController@robots
GET  /status             → HomeController@status
```

### 🔐 Authentication Routes

#### Guest Routes (Not Authenticated)
```
GET  /login                    → LoginController@showLoginForm
POST /login                    → LoginController@login
POST /login/username           → LoginController@loginWithUsername
GET  /register                 → RegisterController@showRegistrationForm
POST /register                 → RegisterController@register
GET  /forgot-password          → LoginController@showResetForm
POST /forgot-password          → LoginController@sendResetLinkEmail
GET  /reset-password/{token}   → LoginController@showResetForm
POST /reset-password           → LoginController@resetPassword
GET  /verify-email/{token}     → RegisterController@verifyEmail
POST /resend-verification      → RegisterController@resendVerification
POST /check-username           → RegisterController@checkUsername
POST /check-email              → RegisterController@checkEmail
GET  /registration-requirements → RegisterController@getRegistrationRequirements
```

#### Authenticated Routes
```
POST /logout                   → LoginController@logout
GET  /confirm-password         → LoginController@showResetForm
POST /confirm-password         → LoginController@resetPassword
GET  /check-auth               → LoginController@checkAuth
POST /refresh                  → LoginController@refresh
GET  /login-history            → LoginController@getLoginHistory
```

### 🤖 AI Assistant Routes (Protected)
```
GET  /ai-assistant             → AiAssistantController@index
POST /ai-assistant/chat        → AiAssistantController@chat
POST /ai-assistant/book-appointment → AiAssistantController@bookAppointment
POST /ai-assistant/medical-advice → AiAssistantController@getMedicalAdvice
POST /ai-assistant/medication-info → AiAssistantController@getMedicationInfo
GET  /ai-assistant/history     → AiAssistantController@getHistory
DELETE /ai-assistant/history   → AiAssistantController@clearHistory
GET  /ai-assistant/suggestions → AiAssistantController@getSuggestions
```

### 👨‍⚕️ Admin Routes (Protected with admin middleware)

#### Dashboard
```
GET  /admin                    → AdminDashboardController@index
GET  /admin/stats              → AdminDashboardController@getStats
GET  /admin/chart-data         → AdminDashboardController@getChartData
GET  /admin/recent-activities  → AdminDashboardController@getRecentActivities
GET  /admin/export-data        → AdminDashboardController@exportData
GET  /admin/system-health      → AdminDashboardController@systemHealth
```

#### User Management
```
GET    /admin/users            → UserManagementController@index
GET    /admin/users/create     → UserManagementController@create
POST   /admin/users            → UserManagementController@store
GET    /admin/users/{id}       → UserManagementController@show
GET    /admin/users/{id}/edit  → UserManagementController@edit
PUT    /admin/users/{id}       → UserManagementController@update
DELETE /admin/users/{id}       → UserManagementController@destroy
POST   /admin/users/bulk-action → UserManagementController@bulkAction
GET    /admin/users/export     → UserManagementController@export
```

#### Payment Management
```
GET    /admin/payments                    → PaymentManagementController@index
GET    /admin/payments/{id}               → PaymentManagementController@show
PUT    /admin/payments/{id}/status        → PaymentManagementController@updateStatus
POST   /admin/payments/{id}/refund        → PaymentManagementController@processRefund
GET    /admin/payments/stats              → PaymentManagementController@getStats
GET    /admin/payments/chart-data         → PaymentManagementController@getChartData
GET    /admin/payments/export             → PaymentManagementController@export
GET    /admin/payments/webhooks           → PaymentManagementController@getWebhooks
POST   /admin/payments/webhooks/{id}/retry → PaymentManagementController@retryWebhook
GET    /admin/payments/financial-report   → PaymentManagementController@getFinancialReport
```

#### Appointment Management
```
GET    /admin/appointments                → AppointmentManagementController@index
GET    /admin/appointments/create         → AppointmentManagementController@create
POST   /admin/appointments                → AppointmentManagementController@store
GET    /admin/appointments/{id}           → AppointmentManagementController@show
GET    /admin/appointments/{id}/edit      → AppointmentManagementController@edit
PUT    /admin/appointments/{id}           → AppointmentManagementController@update
DELETE /admin/appointments/{id}           → AppointmentManagementController@destroy
POST   /admin/appointments/bulk-action    → AppointmentManagementController@bulkAction
GET    /admin/appointments/stats          → AppointmentManagementController@getStats
GET    /admin/appointments/chart-data     → AppointmentManagementController@getChartData
GET    /admin/appointments/available-slots → AppointmentManagementController@getAvailableSlots
GET    /admin/appointments/export         → AppointmentManagementController@export
```

#### Settings Management
```
GET    /admin/settings                    → SettingsController@index
GET    /admin/settings/specialties        → SettingsController@specialties
POST   /admin/settings/specialties        → SettingsController@storeSpecialty
PUT    /admin/settings/specialties/{id}   → SettingsController@updateSpecialty
DELETE /admin/settings/specialties/{id}   → SettingsController@deleteSpecialty
GET    /admin/settings/plans              → SettingsController@plans
POST   /admin/settings/plans              → SettingsController@storePlan
PUT    /admin/settings/plans/{id}         → SettingsController@updatePlan
DELETE /admin/settings/plans/{id}         → SettingsController@deletePlan
GET    /admin/settings/plans/{planId}/features → SettingsController@planFeatures
POST   /admin/settings/plans/{planId}/features → SettingsController@storePlanFeature
PUT    /admin/settings/features/{featureId} → SettingsController@updatePlanFeature
DELETE /admin/settings/features/{featureId} → SettingsController@deletePlanFeature
GET    /admin/settings/system             → SettingsController@systemSettings
PUT    /admin/settings/system             → SettingsController@updateSystemSettings
GET    /admin/settings/pricing            → SettingsController@pricingSettings
PUT    /admin/settings/pricing/specialty/{specialtyId} → SettingsController@updateSpecialtyPricing
PUT    /admin/settings/pricing/doctor/{doctorId} → SettingsController@updateDoctorPricing
POST   /admin/settings/clear-cache        → SettingsController@clearCache
GET    /admin/settings/system-stats       → SettingsController@getSystemStats
```

### 👨‍⚕️ Doctor Routes (Protected with doctor middleware)

#### Dashboard
```
GET  /doctor                    → DoctorDashboardController@index
GET  /doctor/stats              → DoctorDashboardController@getStats
GET  /doctor/chart-data         → DoctorDashboardController@getChartData
GET  /doctor/today-schedule     → DoctorDashboardController@getTodaySchedule
GET  /doctor/upcoming-appointments → DoctorDashboardController@getUpcomingAppointments
GET  /doctor/recent-activities  → DoctorDashboardController@getRecentActivities
GET  /doctor/profile-summary    → DoctorDashboardController@getProfileSummary
GET  /doctor/export-data        → DoctorDashboardController@exportData
```

#### Appointments
```
GET    /doctor/appointments                → DoctorAppointmentController@index
GET    /doctor/appointments/{id}           → DoctorAppointmentController@show
PUT    /doctor/appointments/{id}/status    → DoctorAppointmentController@updateStatus
GET    /doctor/appointments/today          → DoctorAppointmentController@today
GET    /doctor/appointments/upcoming       → DoctorAppointmentController@upcoming
GET    /doctor/appointments/past           → DoctorAppointmentController@past
GET    /doctor/appointments/calendar       → DoctorAppointmentController@calendar
GET    /doctor/appointments/available-slots → DoctorAppointmentController@getAvailableSlots
GET    /doctor/appointments/stats          → DoctorAppointmentController@getStats
GET    /doctor/appointments/export         → DoctorAppointmentController@export
GET    /doctor/appointments/patient/{patientId}/history → DoctorAppointmentController@getPatientHistory
POST   /doctor/appointments/{id}/notes     → DoctorAppointmentController@addNotes
```

#### Patients
```
GET    /doctor/patients                    → PatientController@index
GET    /doctor/patients/{id}               → PatientController@show
GET    /doctor/patients/{patientId}/appointment-history → PatientController@getAppointmentHistory
GET    /doctor/patients/{patientId}/payment-history → PatientController@getPaymentHistory
GET    /doctor/patients/{patientId}/stats  → PatientController@getPatientStats
GET    /doctor/patients/recent             → PatientController@getRecentPatients
GET    /doctor/patients/top                → PatientController@getTopPatients
GET    /doctor/patients/search             → PatientController@search
GET    /doctor/patients/demographics       → PatientController@getDemographics
GET    /doctor/patients/export             → PatientController@export
GET    /doctor/patients/{patientId}/medical-history → PatientController@getMedicalHistory
POST   /doctor/patients/{patientId}/notes  → PatientController@addNotes
GET    /doctor/patients/dashboard-stats    → PatientController@getDashboardStats
```

#### Profile Management
```
GET    /doctor/profile                     → DoctorProfileController@index
PUT    /doctor/profile/update              → DoctorProfileController@updateProfile
PUT    /doctor/profile/account             → DoctorProfileController@updateAccount
PUT    /doctor/profile/password            → DoctorProfileController@changePassword
PUT    /doctor/profile/working-hours       → DoctorProfileController@updateWorkingHours
GET    /doctor/profile/working-hours       → DoctorProfileController@getWorkingHours
POST   /doctor/profile/image               → DoctorProfileController@updateProfileImage
GET    /doctor/profile/stats               → DoctorProfileController@getProfileStats
POST   /doctor/profile/availability        → DoctorProfileController@toggleAvailability
GET    /doctor/profile/reviews             → DoctorProfileController@getReviews
GET    /doctor/profile/export              → DoctorProfileController@exportProfile
```

#### Wallet Management
```
GET    /doctor/wallet                      → DoctorWalletController@index
GET    /doctor/wallet/balance              → DoctorWalletController@getWallet
GET    /doctor/wallet/transactions         → DoctorWalletController@getTransactions
GET    /doctor/wallet/stats                → DoctorWalletController@getStats
POST   /doctor/wallet/withdrawal           → DoctorWalletController@requestWithdrawal
GET    /doctor/wallet/earnings             → DoctorWalletController@getEarnings
GET    /doctor/wallet/earnings-stats       → DoctorWalletController@getEarningsStats
GET    /doctor/wallet/monthly-earnings     → DoctorWalletController@getMonthlyEarnings
GET    /doctor/wallet/withdrawals          → DoctorWalletController@getWithdrawals
DELETE /doctor/wallet/withdrawals/{id}     → DoctorWalletController@cancelWithdrawal
GET    /doctor/wallet/transactions/{id}    → DoctorWalletController@getTransaction
GET    /doctor/wallet/export               → DoctorWalletController@export
```

### 👤 Patient Routes (Protected with patient middleware)

#### Dashboard
```
GET  /patient                    → PatientDashboardController@index
GET  /patient/stats              → PatientDashboardController@getStats
GET  /patient/chart-data         → PatientDashboardController@getChartData
GET  /patient/today-appointments → PatientDashboardController@getTodayAppointments
GET  /patient/upcoming-appointments → PatientDashboardController@getUpcomingAppointments
GET  /patient/recent-activities  → PatientDashboardController@getRecentActivities
GET  /patient/profile-summary    → PatientDashboardController@getProfileSummary
GET  /patient/favorite-doctors   → PatientDashboardController@getFavoriteDoctors
GET  /patient/health-summary     → PatientDashboardController@getHealthSummary
GET  /patient/export-data        → PatientDashboardController@exportData
```

#### Appointments
```
GET    /patient/appointments                → PatientAppointmentController@index
GET    /patient/appointments/{id}           → PatientAppointmentController@show
GET    /patient/appointments/create         → PatientAppointmentController@create
POST   /patient/appointments                → PatientAppointmentController@store
DELETE /patient/appointments/{id}           → PatientAppointmentController@cancel
GET    /patient/appointments/available-doctors → PatientAppointmentController@getAvailableDoctors
GET    /patient/appointments/available-slots → PatientAppointmentController@getAvailableSlots
GET    /patient/appointments/upcoming       → PatientAppointmentController@upcoming
GET    /patient/appointments/past           → PatientAppointmentController@past
GET    /patient/appointments/stats          → PatientAppointmentController@getStats
GET    /patient/appointments/export         → PatientAppointmentController@export
POST   /patient/appointments/{id}/rate      → PatientAppointmentController@rate
```

#### Doctor Browsing
```
GET    /patient/doctors                      → PatientDoctorController@index
GET    /patient/doctors/{id}                 → PatientDoctorController@show
GET    /patient/doctors/search               → PatientDoctorController@search
GET    /patient/doctors/available            → PatientDoctorController@getAvailableDoctors
GET    /patient/doctors/{doctorId}/working-hours → PatientDoctorController@getWorkingHours
GET    /patient/doctors/{doctorId}/available-slots → PatientDoctorController@getAvailableSlots
GET    /patient/doctors/{doctorId}/ratings   → PatientDoctorController@getRatings
GET    /patient/doctors/{doctorId}/stats     → PatientDoctorController@getStats
GET    /patient/doctors/top-rated            → PatientDoctorController@getTopRated
GET    /patient/doctors/specialty/{specialtyId} → PatientDoctorController@getBySpecialty
GET    /patient/doctors/favorites            → PatientDoctorController@getFavoriteDoctors
GET    /patient/doctors/recently-visited     → PatientDoctorController@getRecentlyVisited
```

#### Profile Management
```
GET    /patient/profile                     → PatientProfileController@index
PUT    /patient/profile/update              → PatientProfileController@updateProfile
PUT    /patient/profile/account             → PatientProfileController@updateAccount
PUT    /patient/profile/password            → PatientProfileController@changePassword
POST   /patient/profile/image               → PatientProfileController@uploadImage
GET    /patient/profile/completion          → PatientProfileController@getProfileCompletion
GET    /patient/profile/export              → PatientProfileController@export
```

#### Wallet Management
```
GET    /patient/wallet                      → PatientWalletController@index
GET    /patient/wallet/balance              → PatientWalletController@getWallet
GET    /patient/wallet/transactions         → PatientWalletController@getTransactions
GET    /patient/wallet/stats                → PatientWalletController@getStats
POST   /patient/wallet/add-funds            → PatientWalletController@addFunds
GET    /patient/wallet/payment-history      → PatientWalletController@getPaymentHistory
GET    /patient/wallet/payment-stats        → PatientWalletController@getPaymentStats
GET    /patient/wallet/monthly-spending     → PatientWalletController@getMonthlySpending
GET    /patient/wallet/transactions/{id}    → PatientWalletController@getTransaction
GET    /patient/wallet/payments/{id}        → PatientWalletController@getPayment
POST   /patient/wallet/payments/{paymentId}/refund → PatientWalletController@requestRefund
GET    /patient/wallet/export               → PatientWalletController@export
```

### 🔧 General Protected Routes (All authenticated users)
```
GET    /appointments                → AppointmentController@index
GET    /appointments/create         → AppointmentController@create
POST   /appointments                → AppointmentController@store
GET    /appointments/{id}           → AppointmentController@show
GET    /appointments/{id}/edit      → AppointmentController@edit
PUT    /appointments/{id}           → AppointmentController@update
DELETE /appointments/{id}           → AppointmentController@destroy
GET    /appointments/{id}/available-slots → AppointmentController@getAvailableSlots
PUT    /appointments/{id}/status    → AppointmentController@updateStatus
GET    /appointments/stats          → AppointmentController@getStats
GET    /appointments/export         → AppointmentController@export

GET    /doctors                     → DoctorController@index
GET    /doctors/create              → DoctorController@create
POST   /doctors                     → DoctorController@store
GET    /doctors/{id}                → DoctorController@show
GET    /doctors/{id}/edit           → DoctorController@edit
PUT    /doctors/{id}                → DoctorController@update
DELETE /doctors/{id}                → DoctorController@destroy
GET    /doctors/{id}/working-hours  → DoctorController@getWorkingHours
PUT    /doctors/{id}/working-hours  → DoctorController@updateWorkingHours
GET    /doctors/{id}/reviews        → DoctorController@getReviews
GET    /doctors/search              → DoctorController@search
GET    /doctors/{id}/stats          → DoctorController@getStats
GET    /doctors/export              → DoctorController@export

GET    /payments                    → PaymentController@index
GET    /payments/create             → PaymentController@create
POST   /payments                    → PaymentController@store
GET    /payments/{id}               → PaymentController@show
GET    /payments/{id}/edit          → PaymentController@edit
PUT    /payments/{id}               → PaymentController@update
POST   /payments/{id}/process       → PaymentController@processPayment
POST   /payments/{id}/refund        → PaymentController@processRefund
GET    /payments/stats              → PaymentController@getStats
GET    /payments/export             → PaymentController@export
```

## 🌐 API Routes (`routes/api.php`)

### Public API Routes
```
GET  /api/health              → Health check
GET  /api/specialties         → DoctorController@getSpecialties
GET  /api/featured-doctors    → DoctorController@getFeaturedDoctors
GET  /api/public-stats        → DoctorController@getPublicStats
```

### Authentication API Routes
```
POST /api/login               → LoginController@login
POST /api/login/username      → LoginController@loginWithUsername
POST /api/register            → RegisterController@register
POST /api/check-username      → RegisterController@checkUsername
POST /api/check-email         → RegisterController@checkEmail
GET  /api/registration-requirements → RegisterController@getRegistrationRequirements
POST /api/forgot-password     → LoginController@sendResetLinkEmail
POST /api/reset-password      → LoginController@resetPassword
GET  /api/verify-email/{token} → RegisterController@verifyEmail
POST /api/resend-verification → RegisterController@resendVerification
POST /api/logout              → LoginController@logout
GET  /api/user                → Get authenticated user
GET  /api/check-auth          → LoginController@checkAuth
POST /api/refresh             → LoginController@refresh
GET  /api/login-history       → LoginController@getLoginHistory
POST /api/confirm-password    → LoginController@resetPassword
```

### AI Assistant API Routes
```
POST /api/ai/chat             → AiAssistantController@chat
POST /api/ai/voice            → AiAssistantController@processVoiceInput
GET  /api/ai/health           → AiAssistantController@checkHealth
POST /api/ai/book-appointment → AiAssistantController@bookAppointment
POST /api/ai/medical-advice   → AiAssistantController@getMedicalAdvice
POST /api/ai/medication-info  → AiAssistantController@getMedicationInfo
GET  /api/ai/history          → AiAssistantController@getHistory
DELETE /api/ai/history        → AiAssistantController@clearHistory
GET  /api/ai/suggestions      → AiAssistantController@getSuggestions
```

### Admin API Routes
```
GET  /api/admin/dashboard     → AdminDashboardController@index
GET  /api/admin/stats         → AdminDashboardController@getStats
GET  /api/admin/chart-data    → AdminDashboardController@getChartData
GET  /api/admin/recent-activities → AdminDashboardController@getRecentActivities
GET  /api/admin/export-data   → AdminDashboardController@exportData
GET  /api/admin/system-health → AdminDashboardController@systemHealth

GET    /api/admin/users       → UserManagementController@index
POST   /api/admin/users       → UserManagementController@store
GET    /api/admin/users/{id}  → UserManagementController@show
PUT    /api/admin/users/{id}  → UserManagementController@update
DELETE /api/admin/users/{id}  → UserManagementController@destroy
POST   /api/admin/users/bulk-action → UserManagementController@bulkAction
GET    /api/admin/users/export → UserManagementController@export

GET    /api/admin/payments    → PaymentManagementController@index
GET    /api/admin/payments/{id} → PaymentManagementController@show
PUT    /api/admin/payments/{id}/status → PaymentManagementController@updateStatus
POST   /api/admin/payments/{id}/refund → PaymentManagementController@processRefund
GET    /api/admin/payments/stats → PaymentManagementController@getStats
GET    /api/admin/payments/chart-data → PaymentManagementController@getChartData
GET    /api/admin/payments/export → PaymentManagementController@export
GET    /api/admin/payments/webhooks → PaymentManagementController@getWebhooks
POST   /api/admin/payments/webhooks/{id}/retry → PaymentManagementController@retryWebhook
GET    /api/admin/payments/financial-report → PaymentManagementController@getFinancialReport

GET    /api/admin/appointments → AppointmentManagementController@index
POST   /api/admin/appointments → AppointmentManagementController@store
GET    /api/admin/appointments/{id} → AppointmentManagementController@show
PUT    /api/admin/appointments/{id} → AppointmentManagementController@update
DELETE /api/admin/appointments/{id} → AppointmentManagementController@destroy
POST   /api/admin/appointments/bulk-action → AppointmentManagementController@bulkAction
GET    /api/admin/appointments/stats → AppointmentManagementController@getStats
GET    /api/admin/appointments/chart-data → AppointmentManagementController@getChartData
GET    /api/admin/appointments/available-slots → AppointmentManagementController@getAvailableSlots
GET    /api/admin/appointments/export → AppointmentManagementController@export

GET    /api/admin/settings    → SettingsController@index
GET    /api/admin/settings/specialties → SettingsController@specialties
POST   /api/admin/settings/specialties → SettingsController@storeSpecialty
PUT    /api/admin/settings/specialties/{id} → SettingsController@updateSpecialty
DELETE /api/admin/settings/specialties/{id} → SettingsController@deleteSpecialty
GET    /api/admin/settings/plans → SettingsController@plans
POST   /api/admin/settings/plans → SettingsController@storePlan
PUT    /api/admin/settings/plans/{id} → SettingsController@updatePlan
DELETE /api/admin/settings/plans/{id} → SettingsController@deletePlan
GET    /api/admin/settings/plans/{planId}/features → SettingsController@planFeatures
POST   /api/admin/settings/plans/{planId}/features → SettingsController@storePlanFeature
PUT    /api/admin/settings/features/{featureId} → SettingsController@updatePlanFeature
DELETE /api/admin/settings/features/{featureId} → SettingsController@deletePlanFeature
GET    /api/admin/settings/system → SettingsController@systemSettings
PUT    /api/admin/settings/system → SettingsController@updateSystemSettings
GET    /api/admin/settings/pricing → SettingsController@pricingSettings
PUT    /api/admin/settings/pricing/specialty/{specialtyId} → SettingsController@updateSpecialtyPricing
PUT    /api/admin/settings/pricing/doctor/{doctorId} → SettingsController@updateDoctorPricing
POST   /api/admin/settings/clear-cache → SettingsController@clearCache
GET    /api/admin/settings/system-stats → SettingsController@getSystemStats
```

### Doctor API Routes
```
GET  /api/doctor/dashboard    → DoctorDashboardController@index
GET  /api/doctor/stats        → DoctorDashboardController@getStats
GET  /api/doctor/chart-data   → DoctorDashboardController@getChartData
GET  /api/doctor/today-schedule → DoctorDashboardController@getTodaySchedule
GET  /api/doctor/upcoming-appointments → DoctorDashboardController@getUpcomingAppointments
GET  /api/doctor/recent-activities → DoctorDashboardController@getRecentActivities
GET  /api/doctor/profile-summary → DoctorDashboardController@getProfileSummary
GET  /api/doctor/export-data  → DoctorDashboardController@exportData

GET    /api/doctor/appointments → DoctorAppointmentController@index
GET    /api/doctor/appointments/{id} → DoctorAppointmentController@show
PUT    /api/doctor/appointments/{id}/status → DoctorAppointmentController@updateStatus
GET    /api/doctor/appointments/today → DoctorAppointmentController@today
GET    /api/doctor/appointments/upcoming → DoctorAppointmentController@upcoming
GET    /api/doctor/appointments/past → DoctorAppointmentController@past
GET    /api/doctor/appointments/calendar → DoctorAppointmentController@calendar
GET    /api/doctor/appointments/available-slots → DoctorAppointmentController@getAvailableSlots
GET    /api/doctor/appointments/stats → DoctorAppointmentController@getStats
GET    /api/doctor/appointments/export → DoctorAppointmentController@export
GET    /api/doctor/appointments/patient/{patientId}/history → DoctorAppointmentController@getPatientHistory
POST   /api/doctor/appointments/{id}/notes → DoctorAppointmentController@addNotes

GET    /api/doctor/patients   → PatientController@index
GET    /api/doctor/patients/{id} → PatientController@show
GET    /api/doctor/patients/{patientId}/appointment-history → PatientController@getAppointmentHistory
GET    /api/doctor/patients/{patientId}/payment-history → PatientController@getPaymentHistory
GET    /api/doctor/patients/{patientId}/stats → PatientController@getPatientStats
GET    /api/doctor/patients/recent → PatientController@getRecentPatients
GET    /api/doctor/patients/top → PatientController@getTopPatients
GET    /api/doctor/patients/search → PatientController@search
GET    /api/doctor/patients/demographics → PatientController@getDemographics
GET    /api/doctor/patients/export → PatientController@export
GET    /api/doctor/patients/{patientId}/medical-history → PatientController@getMedicalHistory
POST   /api/doctor/patients/{patientId}/notes → PatientController@addNotes
GET    /api/doctor/patients/dashboard-stats → PatientController@getDashboardStats

GET    /api/doctor/profile    → DoctorProfileController@index
PUT    /api/doctor/profile/update → DoctorProfileController@updateProfile
PUT    /api/doctor/profile/account → DoctorProfileController@updateAccount
PUT    /api/doctor/profile/password → DoctorProfileController@changePassword
PUT    /api/doctor/profile/working-hours → DoctorProfileController@updateWorkingHours
GET    /api/doctor/profile/working-hours → DoctorProfileController@getWorkingHours
POST   /api/doctor/profile/image → DoctorProfileController@updateProfileImage
GET    /api/doctor/profile/stats → DoctorProfileController@getProfileStats
POST   /api/doctor/profile/availability → DoctorProfileController@toggleAvailability
GET    /api/doctor/profile/reviews → DoctorProfileController@getReviews
GET    /api/doctor/profile/export → DoctorProfileController@exportProfile

GET    /api/doctor/wallet     → DoctorWalletController@index
GET    /api/doctor/wallet/balance → DoctorWalletController@getWallet
GET    /api/doctor/wallet/transactions → DoctorWalletController@getTransactions
GET    /api/doctor/wallet/stats → DoctorWalletController@getStats
POST   /api/doctor/wallet/withdrawal → DoctorWalletController@requestWithdrawal
GET    /api/doctor/wallet/earnings → DoctorWalletController@getEarnings
GET    /api/doctor/wallet/earnings-stats → DoctorWalletController@getEarningsStats
GET    /api/doctor/wallet/monthly-earnings → DoctorWalletController@getMonthlyEarnings
GET    /api/doctor/wallet/withdrawals → DoctorWalletController@getWithdrawals
DELETE /api/doctor/wallet/withdrawals/{id} → DoctorWalletController@cancelWithdrawal
GET    /api/doctor/wallet/transactions/{id} → DoctorWalletController@getTransaction
GET    /api/doctor/wallet/export → DoctorWalletController@export
```

### Patient API Routes
```
GET  /api/patient/dashboard   → PatientDashboardController@index
GET  /api/patient/stats       → PatientDashboardController@getStats
GET  /api/patient/chart-data  → PatientDashboardController@getChartData
GET  /api/patient/today-appointments → PatientDashboardController@getTodayAppointments
GET  /api/patient/upcoming-appointments → PatientDashboardController@getUpcomingAppointments
GET  /api/patient/recent-activities → PatientDashboardController@getRecentActivities
GET  /api/patient/profile-summary → PatientDashboardController@getProfileSummary
GET  /api/patient/favorite-doctors → PatientDashboardController@getFavoriteDoctors
GET  /api/patient/health-summary → PatientDashboardController@getHealthSummary
GET  /api/patient/export-data → PatientDashboardController@exportData

GET    /api/patient/appointments → PatientAppointmentController@index
GET    /api/patient/appointments/{id} → PatientAppointmentController@show
POST   /api/patient/appointments → PatientAppointmentController@store
DELETE /api/patient/appointments/{id} → PatientAppointmentController@cancel
GET    /api/patient/appointments/available-doctors → PatientAppointmentController@getAvailableDoctors
GET    /api/patient/appointments/available-slots → PatientAppointmentController@getAvailableSlots
GET    /api/patient/appointments/upcoming → PatientAppointmentController@upcoming
GET    /api/patient/appointments/past → PatientAppointmentController@past
GET    /api/patient/appointments/stats → PatientAppointmentController@getStats
GET    /api/patient/appointments/export → PatientAppointmentController@export
POST   /api/patient/appointments/{id}/rate → PatientAppointmentController@rate

GET    /api/patient/doctors   → PatientDoctorController@index
GET    /api/patient/doctors/{id} → PatientDoctorController@show
GET    /api/patient/doctors/search → PatientDoctorController@search
GET    /api/patient/doctors/available → PatientDoctorController@getAvailableDoctors
GET    /api/patient/doctors/{doctorId}/working-hours → PatientDoctorController@getWorkingHours
GET    /api/patient/doctors/{doctorId}/available-slots → PatientDoctorController@getAvailableSlots
GET    /api/patient/doctors/{doctorId}/ratings → PatientDoctorController@getRatings
GET    /api/patient/doctors/{doctorId}/stats → PatientDoctorController@getStats
GET    /api/patient/doctors/top-rated → PatientDoctorController@getTopRated
GET    /api/patient/doctors/specialty/{specialtyId} → PatientDoctorController@getBySpecialty
GET    /api/patient/doctors/favorites → PatientDoctorController@getFavoriteDoctors
GET    /api/patient/doctors/recently-visited → PatientDoctorController@getRecentlyVisited

GET    /api/patient/profile   → PatientProfileController@index
PUT    /api/patient/profile/update → PatientProfileController@updateProfile
PUT    /api/patient/profile/account → PatientProfileController@updateAccount
PUT    /api/patient/profile/password → PatientProfileController@changePassword
POST   /api/patient/profile/image → PatientProfileController@uploadImage
GET    /api/patient/profile/completion → PatientProfileController@getProfileCompletion
GET    /api/patient/profile/export → PatientProfileController@export

GET    /api/patient/wallet    → PatientWalletController@index
GET    /api/patient/wallet/balance → PatientWalletController@getWallet
GET    /api/patient/wallet/transactions → PatientWalletController@getTransactions
GET    /api/patient/wallet/stats → PatientWalletController@getStats
POST   /api/patient/wallet/add-funds → PatientWalletController@addFunds
GET    /api/patient/wallet/payment-history → PatientWalletController@getPaymentHistory
GET    /api/patient/wallet/payment-stats → PatientWalletController@getPaymentStats
GET    /api/patient/wallet/monthly-spending → PatientWalletController@getMonthlySpending
GET    /api/patient/wallet/transactions/{id} → PatientWalletController@getTransaction
GET    /api/patient/wallet/payments/{id} → PatientWalletController@getPayment
POST   /api/patient/wallet/payments/{paymentId}/refund → PatientWalletController@requestRefund
GET    /api/patient/wallet/export → PatientWalletController@export
```

### General API Routes (All authenticated users)
```
GET    /api/appointments      → AppointmentController@index
POST   /api/appointments      → AppointmentController@store
GET    /api/appointments/{id} → AppointmentController@show
PUT    /api/appointments/{id} → AppointmentController@update
DELETE /api/appointments/{id} → AppointmentController@destroy
GET    /api/appointments/{id}/available-slots → AppointmentController@getAvailableSlots
PUT    /api/appointments/{id}/status → AppointmentController@updateStatus
GET    /api/appointments/stats → AppointmentController@getStats
GET    /api/appointments/export → AppointmentController@export

GET    /api/doctors           → DoctorController@index
POST   /api/doctors           → DoctorController@store
GET    /api/doctors/{id}      → DoctorController@show
PUT    /api/doctors/{id}      → DoctorController@update
DELETE /api/doctors/{id}      → DoctorController@destroy
GET    /api/doctors/{id}/working-hours → DoctorController@getWorkingHours
PUT    /api/doctors/{id}/working-hours → DoctorController@updateWorkingHours
GET    /api/doctors/{id}/reviews → DoctorController@getReviews
GET    /api/doctors/search    → DoctorController@search
GET    /api/doctors/{id}/stats → DoctorController@getStats
GET    /api/doctors/export    → DoctorController@export

GET    /api/payments          → PaymentController@index
POST   /api/payments          → PaymentController@store
GET    /api/payments/{id}     → PaymentController@show
PUT    /api/payments/{id}     → PaymentController@update
POST   /api/payments/{id}/process → PaymentController@processPayment
POST   /api/payments/{id}/refund → PaymentController@processRefund
GET    /api/payments/stats    → PaymentController@getStats
GET    /api/payments/export   → PaymentController@export
```

### Webhook Routes
```
POST /api/webhooks/stripe     → Stripe webhook handler
POST /api/webhooks/paypal     → PayPal webhook handler
```

## 🔧 Middleware Registration

To register the custom middleware, add the following to your `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ... existing middleware
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'doctor' => \App\Http\Middleware\DoctorMiddleware::class,
    'patient' => \App\Http\Middleware\PatientMiddleware::class,
    'ai.health' => \App\Http\Middleware\AiServiceHealthCheck::class,
];
```

## 🎯 Route Features

### ✅ Implemented Features
- **Role-based Access Control**: Admin, Doctor, Patient routes
- **Authentication Protection**: Login required for protected routes
- **API Endpoints**: RESTful API for all functionality
- **Webhook Support**: Payment processing webhooks
- **Health Checks**: System and AI service health monitoring
- **Export Functionality**: Data export for all major entities
- **Search & Filtering**: Advanced search capabilities
- **Statistics & Analytics**: Comprehensive reporting
- **File Upload**: Profile images and documents
- **Real-time Features**: Live updates and notifications

### 🔄 Route Patterns
- **RESTful Design**: Standard CRUD operations
- **Consistent Naming**: Clear and descriptive route names
- **Proper HTTP Methods**: GET, POST, PUT, DELETE used appropriately
- **Nested Resources**: Proper resource relationships
- **Bulk Operations**: Support for multiple item operations
- **Pagination**: Built-in pagination support
- **Filtering**: Query parameter filtering
- **Sorting**: Order by parameters

## 🚀 Usage Examples

### Web Routes
```php
// Navigate to admin dashboard
Route::get('/admin', [AdminDashboardController::class, 'index']);

// Create new appointment
Route::post('/appointments', [AppointmentController::class, 'store']);

// Update doctor profile
Route::put('/doctor/profile/update', [DoctorProfileController::class, 'updateProfile']);
```

### API Routes
```php
// Get user data
GET /api/user

// Create appointment via API
POST /api/appointments
{
    "doctor_id": 1,
    "appointment_date": "2024-01-15",
    "appointment_time": "10:00:00"
}

// Update payment status
PUT /api/admin/payments/1/status
{
    "status": "succeeded"
}
```

## 📊 Route Statistics

- **Total Web Routes**: 200+
- **Total API Routes**: 150+
- **Protected Routes**: 80%
- **Public Routes**: 20%
- **Admin Routes**: 50+
- **Doctor Routes**: 40+
- **Patient Routes**: 35+
- **General Routes**: 30+

## 🎉 Conclusion

This comprehensive routing system provides a solid foundation for the Medical Booking System, ensuring proper access control, security, and functionality for all user types. The routes are well-organized, follow RESTful conventions, and support all the advanced features of the platform.

**Created with ❤️ by Hawraa Ahmad Balwi**

---

*This documentation covers all routes in the Medical Booking System. For implementation details, refer to the individual controller files.*
