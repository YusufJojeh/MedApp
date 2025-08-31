# AI Booking Integration Guide

## Overview

The AI Booking system provides an end-to-end flow for booking appointments using natural language processing and wallet integration. This guide covers how to integrate and use the system.

## Features

- **AI Intent Detection**: Understands user booking requests
- **Doctor Browsing**: Search doctors by specialty
- **Availability Checking**: Real-time slot availability
- **Atomic Booking**: Secure appointment booking with wallet deduction
- **Feature Flags**: Safe deployment with environment-based toggles

## API Endpoints

### 1. Feature Status Check
```http
GET /api/ai-booking/feature-status
```

**Response:**
```json
{
  "success": true,
  "features": {
    "ai_booking_enabled": true,
    "ai_proxy_enabled": true,
    "wallet_integration_enabled": true,
    "ai_service_url": "http://127.0.0.1:5005"
  }
}
```

### 2. Process AI Booking Intent
```http
POST /api/ai-booking/process-intent
Authorization: Bearer {token}
Content-Type: application/json

{
  "message": "I want to book an appointment with a cardiologist"
}
```

**Response:**
```json
{
  "success": true,
  "message": "I found some doctors for you. Here are the available options:",
  "intent": "book_appointment",
  "doctors": [
    {
      "id": 1,
      "name": "Dr. John Smith",
      "consultation_fee": 150.00,
      "rating": 4.8,
      "experience_years": 15,
      "specialty": "Cardiology",
      "description": "Experienced cardiologist..."
    }
  ],
  "suggestions": [
    "Select a doctor from the list above",
    "Tell me your preferred date and time",
    "Ask about consultation fees"
  ]
}
```

### 3. Get Available Doctors
```http
GET /api/ai-booking/doctors?specialty=cardiology&limit=10
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "doctors": [
    {
      "id": 1,
      "name": "Dr. John Smith",
      "consultation_fee": 150.00,
      "rating": 4.8,
      "experience_years": 15,
      "specialty": "Cardiology",
      "description": "Experienced cardiologist..."
    }
  ]
}
```

### 4. Check Doctor Availability
```http
POST /api/ai-booking/check-availability
Authorization: Bearer {token}
Content-Type: application/json

{
  "doctor_id": 1,
  "date": "2024-01-15"
}
```

**Response:**
```json
{
  "success": true,
  "availability": [
    {
      "time": "09:00:00",
      "formatted_time": "9:00 AM",
      "available": true
    },
    {
      "time": "09:30:00",
      "formatted_time": "9:30 AM",
      "available": true
    }
  ],
  "date": "2024-01-15"
}
```

### 5. Book Appointment with Wallet
```http
POST /api/ai-booking/book-appointment
Authorization: Bearer {token}
Content-Type: application/json

{
  "doctor_id": 1,
  "date": "2024-01-15",
  "time": "09:00:00"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Appointment booked successfully with Dr. John Smith on 2024-01-15 at 9:00 AM",
  "appointment_id": 123,
  "consultation_fee": 150.00,
  "wallet_balance": 350.00
}
```

### 6. Get Booking Suggestions
```http
GET /api/ai-booking/suggestions
```

**Response:**
```json
{
  "success": true,
  "suggestions": [
    "Browse doctors by specialty",
    "Check appointment availability",
    "View consultation fees",
    "Add funds to wallet",
    "Book an appointment",
    "Check my wallet balance"
  ]
}
```

## Frontend Integration

### 1. Check Feature Status
Always check if AI booking is enabled before showing booking options:

```javascript
async function checkAIBookingStatus() {
  try {
    const response = await fetch('/api/ai-booking/feature-status');
    const data = await response.json();
    
    if (data.success && data.features.ai_booking_enabled) {
      // Show AI booking options
      showAIBookingInterface();
    } else {
      // Show traditional booking interface
      showTraditionalBookingInterface();
    }
  } catch (error) {
    console.error('Error checking AI booking status:', error);
  }
}
```

### 2. Process User Intent
Handle natural language booking requests:

```javascript
async function processBookingIntent(message) {
  try {
    const response = await fetch('/api/ai-booking/process-intent', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${getAuthToken()}`,
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ message })
    });
    
    const data = await response.json();
    
    if (data.success) {
      if (data.doctors && data.doctors.length > 0) {
        displayDoctors(data.doctors);
      }
      if (data.suggestions) {
        displaySuggestions(data.suggestions);
      }
    } else {
      showError(data.message);
    }
  } catch (error) {
    console.error('Error processing intent:', error);
  }
}
```

### 3. Display Doctors
Show available doctors to the user:

```javascript
function displayDoctors(doctors) {
  const container = document.getElementById('doctors-container');
  container.innerHTML = '';
  
  doctors.forEach(doctor => {
    const doctorCard = `
      <div class="doctor-card">
        <h3>${doctor.name}</h3>
        <p>Specialty: ${doctor.specialty}</p>
        <p>Rating: ${doctor.rating}/5</p>
        <p>Experience: ${doctor.experience_years} years</p>
        <p>Fee: $${doctor.consultation_fee}</p>
        <button onclick="selectDoctor(${doctor.id})">Select Doctor</button>
      </div>
    `;
    container.innerHTML += doctorCard;
  });
}
```

### 4. Check Availability
Check available time slots for a selected doctor:

```javascript
async function checkAvailability(doctorId, date) {
  try {
    const response = await fetch('/api/ai-booking/check-availability', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${getAuthToken()}`,
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ doctor_id: doctorId, date })
    });
    
    const data = await response.json();
    
    if (data.success) {
      displayTimeSlots(data.availability);
    } else {
      showError(data.message);
    }
  } catch (error) {
    console.error('Error checking availability:', error);
  }
}
```

### 5. Book Appointment
Complete the booking with wallet integration:

```javascript
async function bookAppointment(doctorId, date, time) {
  try {
    const response = await fetch('/api/ai-booking/book-appointment', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${getAuthToken()}`,
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({
        doctor_id: doctorId,
        date: date,
        time: time
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showSuccess(data.message);
      updateWalletBalance(data.wallet_balance);
      // Redirect to appointment confirmation
      window.location.href = `/appointments/${data.appointment_id}`;
    } else {
      showError(data.message);
    }
  } catch (error) {
    console.error('Error booking appointment:', error);
  }
}
```

## Environment Configuration

Add these variables to your `.env` file:

```env
# AI Service Configuration
AI_FLASK_URL=http://127.0.0.1:5005
AI_TIMEOUT=10
AI_CACHE_TTL=3600

# Feature Flags
AI_BOOKING_ENABLED=true
AI_WALLET_INTEGRATION=true
AI_PROXY_ENABLED=true
```

## Error Handling

### Common Error Responses

1. **Feature Disabled**
```json
{
  "success": false,
  "message": "AI booking is currently disabled",
  "feature_disabled": true
}
```

2. **Authentication Required**
```json
{
  "success": false,
  "message": "Authentication required"
}
```

3. **Insufficient Wallet Balance**
```json
{
  "success": false,
  "message": "Insufficient wallet balance. Required: $150, Available: $100"
}
```

4. **Time Slot Not Available**
```json
{
  "success": false,
  "message": "Selected time slot is not available"
}
```

## Security Considerations

1. **Authentication**: All booking endpoints require authentication
2. **CSRF Protection**: Include CSRF tokens in POST requests
3. **Input Validation**: All inputs are validated server-side
4. **Database Transactions**: Atomic operations ensure data consistency
5. **Feature Flags**: Safe deployment with environment-based toggles

## Testing

Use the provided test script to verify the system:

```bash
php test_ai_booking_flow.php
```

## Troubleshooting

### Common Issues

1. **Flask Service Not Responding**
   - Check if Flask is running on port 5005
   - Verify the AI_FLASK_URL in configuration

2. **Authentication Errors**
   - Ensure user is logged in
   - Check if auth token is valid

3. **Feature Disabled**
   - Set AI_BOOKING_ENABLED=true in environment
   - Restart Laravel application

4. **Wallet Integration Issues**
   - Ensure AI_WALLET_INTEGRATION=true
   - Check if user has a wallet
   - Verify sufficient balance

## Support

For issues or questions:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Verify Flask service status: `http://127.0.0.1:5005/health`
3. Test feature status: `GET /api/ai-booking/feature-status`
