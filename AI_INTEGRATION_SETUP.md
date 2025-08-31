# AI Integration Setup Guide

This guide explains how to set up and run the AI integration between the Laravel application and the Python Flask AI service.

## Overview

The system consists of:
- **Laravel Application**: Runs on `http://127.0.0.1:8000`
- **Flask AI Service**: Runs on `http://127.0.0.1:5005`
- **Integration**: Laravel proxies AI requests to Flask service

## Prerequisites

### Python Environment
- Python 3.8 or higher
- pip (Python package manager)

### Laravel Environment
- PHP 8.1 or higher
- Composer
- Laravel 10.x

## Setup Instructions

### 1. Flask AI Service Setup

#### Option A: Automatic Setup (Recommended)
```bash
# Navigate to the nlp directory
cd nlp

# Run the startup script (installs dependencies and starts service)
python start_flask.py
```

**Windows Users:**
```cmd
# Run the batch script from the project root
start_ai_service.bat
```

**Unix/Linux/Mac Users:**
```bash
# Run the shell script from the project root
./start_ai_service.sh
```

#### Option B: Manual Setup
```bash
# Navigate to the nlp directory
cd nlp

# Create virtual environment (optional but recommended)
python -m venv venv

# Activate virtual environment
# On Windows:
venv\Scripts\activate
# On Unix/Linux/Mac:
source venv/bin/activate

# Install Python dependencies
pip install -r requirements.txt

# Start the Flask service
python server.py
```

#### Verify Flask Service
The Flask service should start and display:
```
🚀 Starting Enhanced Medical Voice Assistant NLP Service…
✅ Models loaded successfully
✅ Data loaded successfully
✅ Health tips categories loaded: [list of categories]
🌐 Starting Flask server on port 5005…
```

### 2. Laravel Application Setup

#### Start Laravel Development Server
```bash
# In the project root directory
php artisan serve
```

The Laravel application will be available at `http://127.0.0.1:8000`

### 3. Testing the Integration

#### Test Flask Service Directly
```bash
# Test health endpoint
curl http://127.0.0.1:5005/health

# Test process endpoint
curl -X POST http://127.0.0.1:5005/process \
  -H "Content-Type: application/json" \
  -d '{"text": "I have a headache"}'
```

#### Test Laravel Proxy
```bash
# Test health endpoint via Laravel
curl http://127.0.0.1:8000/api/ai/health

# Test process endpoint via Laravel
curl -X POST http://127.0.0.1:8000/api/ai/process \
  -H "Content-Type: application/json" \
  -d '{"text": "I have a headache"}'
```

#### Test Frontend Integration
1. Open `http://127.0.0.1:8000/ai-assistant` in your browser
2. Log in to the application
3. Type a message in the AI assistant chat
4. Verify that responses are received

## Architecture

### Communication Flow
```
Frontend (JavaScript) → Laravel API → Flask AI Service
```

### API Endpoints

#### Flask Service (Direct)
- `GET http://127.0.0.1:5005/health` - Health check
- `POST http://127.0.0.1:5005/process` - Process text

#### Laravel Proxy
- `GET /api/ai/health` - Health check (proxies to Flask)
- `POST /api/ai/process` - Process text (proxies to Flask)

### Frontend Integration
The frontend uses Laravel proxy endpoints to avoid CORS issues:
- All requests go through `/api/ai/*` endpoints
- CSRF token is included for security
- Error handling is implemented for network failures

## Troubleshooting

### Flask Service Issues

#### Port Already in Use
```bash
# Check what's using port 5005
lsof -i :5005

# Kill the process if needed
kill -9 <PID>
```

#### Missing Dependencies
```bash
# Reinstall dependencies
cd nlp
pip install -r requirements.txt --force-reinstall
```

#### Model Loading Errors
- Check that model files exist in `nlp/models/` directory
- Ensure sufficient disk space and memory
- Check Python version compatibility

### Laravel Issues

#### CSRF Token Errors
- Ensure the Blade template includes the CSRF meta tag:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

#### Proxy Connection Errors
- Verify Flask service is running on port 5005
- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Test direct Flask connection first

### CORS Issues
The integration uses Laravel proxy to avoid CORS issues. If you encounter CORS errors:
1. Ensure you're using the Laravel proxy endpoints (`/api/ai/*`)
2. Don't make direct calls to Flask from frontend
3. Check that Flask CORS is properly configured

## Development

### Adding New AI Features
1. Add new endpoints to `nlp/server.py`
2. Add corresponding proxy routes in `routes/api.php`
3. Update frontend JavaScript to use new endpoints

### Debugging
- Flask logs: Check `nlp/logs/assistant.log`
- Laravel logs: Check `storage/logs/laravel.log`
- Browser console: Check for JavaScript errors
- Network tab: Monitor API requests

## Production Deployment

### Flask Service
- Use a production WSGI server (Gunicorn, uWSGI)
- Set up proper logging and monitoring
- Configure environment variables for production settings

### Laravel Application
- Use proper web server (Nginx, Apache)
- Configure caching and optimization
- Set up SSL certificates

### Security Considerations
- Use HTTPS in production
- Implement rate limiting
- Add authentication to Flask service if needed
- Validate all input data

## Support

For issues or questions:
1. Check the logs for error messages
2. Verify both services are running
3. Test endpoints individually
4. Check network connectivity between services
