# 🤖 AI Integration Guide for Medical Booking System

## Overview

This guide explains how to integrate the AI assistant service with your Laravel medical booking system. The integration provides intelligent medical assistance, appointment booking help, and symptom analysis.

## 🏗️ Architecture

```
┌─────────────────┐    HTTP/JSON    ┌─────────────────┐
│   Laravel App   │ ──────────────► │  Flask AI API   │
│                 │                 │                 │
│ • Controllers   │                 │ • NLP Models    │
│ • Services      │                 │ • Intent Class. │
│ • Models        │                 │ • Q&A System    │
│ • Middleware    │                 │ • Doctor Sug.   │
└─────────────────┘                 └─────────────────┘
```

## 📁 File Structure

```
app/
├── Services/
│   └── AiAssistantService.php      # Main AI service class
├── Http/
│   ├── Controllers/
│   │   └── AiAssistantController.php # AI controller
│   └── Middleware/
│       └── AiServiceHealthCheck.php  # Health check middleware
├── Models/
│   └── AiConversation.php          # AI conversation model
└── Console/Commands/
    └── TestAiIntegration.php       # Test command

database/
└── migrations/
    └── 2024_01_01_000000_create_ai_conversations_table.php

config/
└── services.php                    # AI service configuration

routes/
└── web.php                         # AI routes
```

## 🚀 Installation & Setup

### 1. Environment Variables

Add these to your `.env` file:

```env
# AI Service Configuration
AI_FLASK_URL=http://127.0.0.1:5005
AI_TIMEOUT=10
AI_CACHE_TTL=3600
AI_ENABLED=true
AI_FALLBACK_ENABLED=true
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Register Middleware

Add to `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ... other middlewares
    'ai.health' => \App\Http\Middleware\AiServiceHealthCheck::class,
];
```

### 4. Register Service Provider

Add to `config/app.php`:

```php
'providers' => [
    // ... other providers
    App\Providers\AppServiceProvider::class,
],
```

## 🔧 Configuration

### Services Configuration (`config/services.php`)

```php
'ai' => [
    'flask_url' => env('AI_FLASK_URL', 'http://127.0.0.1:5005'),
    'timeout' => env('AI_TIMEOUT', 10),
    'cache_ttl' => env('AI_CACHE_TTL', 3600), // 1 hour in seconds
    'enabled' => env('AI_ENABLED', true),
    'fallback_enabled' => env('AI_FALLBACK_ENABLED', true),
],
```

## 📡 API Endpoints

### AI Assistant Routes

```php
// Web Routes
Route::prefix('ai-assistant')->name('ai.')->middleware(['auth'])->group(function () {
    Route::get('/', [AiAssistantController::class, 'index'])->name('index');
    Route::post('/chat', [AiAssistantController::class, 'chat'])->name('chat');
    Route::post('/book-appointment', [AiAssistantController::class, 'bookAppointment'])->name('book-appointment');
    Route::post('/medical-advice', [AiAssistantController::class, 'getMedicalAdvice'])->name('medical-advice');
    Route::post('/medication-info', [AiAssistantController::class, 'getMedicationInfo'])->name('medication-info');
    Route::get('/history', [AiAssistantController::class, 'getHistory'])->name('history');
    Route::delete('/history', [AiAssistantController::class, 'clearHistory'])->name('clear-history');
    Route::get('/suggestions', [AiAssistantController::class, 'getSuggestions'])->name('suggestions');
});

// API Routes
Route::prefix('api/ai')->name('api.ai.')->group(function () {
    Route::post('/chat', [AiAssistantController::class, 'chat'])->name('chat');
    Route::post('/voice', [AiAssistantController::class, 'processVoiceInput'])->name('voice');
    Route::get('/health', [AiAssistantController::class, 'checkHealth'])->name('health');
});
```

## 🎯 Usage Examples

### 1. Basic Chat Integration

```php
use App\Services\AiAssistantService;

class SomeController extends Controller
{
    protected $aiService;

    public function __construct(AiAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function chat(Request $request)
    {
        $message = $request->input('message');
        $userId = auth()->id();

        $result = $this->aiService->processText($message, $userId);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'response' => $result['data']['qa']['answer'] ?? 'No response',
                'intent' => $result['data']['intent']['intent'] ?? 'unknown'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'AI service unavailable'
        ], 503);
    }
}
```

### 2. Appointment Booking Assistance

```php
public function getAppointmentSuggestions(Request $request)
{
    $specialty = $request->input('specialty');
    $preferredDate = $request->input('preferred_date');
    $urgency = $request->input('urgency');

    $suggestions = $this->aiService->getAppointmentSuggestions(
        $specialty, 
        $preferredDate, 
        $urgency
    );

    return response()->json([
        'success' => true,
        'suggestions' => $suggestions
    ]);
}
```

### 3. Medical Advice

```php
public function getMedicalAdvice(Request $request)
{
    $symptoms = $request->input('symptoms');
    $duration = $request->input('duration');
    $severity = $request->input('severity');

    $advice = $this->aiService->analyzeSymptoms($symptoms, $duration, $severity);
    $specialists = $this->aiService->getRelevantSpecialists($symptoms);
    $urgency = $this->aiService->assessUrgency($symptoms, $severity);

    return response()->json([
        'success' => true,
        'advice' => $advice,
        'specialists' => $specialists,
        'urgency' => $urgency
    ]);
}
```

### 4. Voice Input Processing

```php
public function processVoice(Request $request)
{
    $transcript = $request->input('transcript');
    $userId = auth()->id();

    $result = $this->aiService->processVoiceInput($transcript, $userId);

    return response()->json($result);
}
```

## 🧪 Testing

### 1. Test AI Integration

```bash
php artisan ai:test
```

### 2. Test with Custom Message

```bash
php artisan ai:test --message="I need to book an appointment with a cardiologist"
```

### 3. Manual Testing

```php
// Test health check
$health = $aiService->checkHealth();

// Test intent prediction
$intent = $aiService->predictIntent("I need to book an appointment");

// Test medical Q&A
$qa = $aiService->answerQA("What are the symptoms of diabetes?");

// Test doctor suggestions
$doctors = $aiService->suggestDoctors("Cardiology");
```

## 🔍 Monitoring & Logging

### 1. Health Monitoring

The system automatically monitors AI service health:

```php
// Check if AI service is available
$isAvailable = $request->attributes->get('ai_service_available', false);

if (!$isAvailable) {
    // Use fallback functionality
    return $this->fallbackResponse();
}
```

### 2. Logging

AI service errors are logged:

```php
// Check logs for AI service issues
tail -f storage/logs/laravel.log | grep "AI Service"
```

### 3. Cache Management

AI responses are cached for performance:

```php
// Clear AI cache
php artisan cache:clear

// Clear specific AI cache
Cache::forget('ai_intent_' . md5($text));
```

## 🛡️ Security & Error Handling

### 1. Input Validation

```php
$validator = Validator::make($request->all(), [
    'message' => 'required|string|max:1000',
    'context' => 'nullable|string|max:500',
]);
```

### 2. Error Handling

```php
try {
    $result = $this->aiService->processText($message, $userId);
    
    if ($result['success']) {
        return response()->json($result);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'AI service error: ' . $result['error']
        ], 500);
    }
} catch (\Exception $e) {
    Log::error('AI Service Error: ' . $e->getMessage());
    
    return response()->json([
        'success' => false,
        'message' => 'Service temporarily unavailable'
    ], 503);
}
```

### 3. Rate Limiting

```php
// Add rate limiting to AI routes
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::post('/ai-assistant/chat', [AiAssistantController::class, 'chat']);
});
```

## 🔄 Fallback Strategy

When AI service is unavailable:

1. **Graceful Degradation**: Continue with basic functionality
2. **Cached Responses**: Use previously cached AI responses
3. **Local Processing**: Use local symptom analysis and doctor suggestions
4. **User Notification**: Inform users about reduced functionality

## 📊 Analytics & Metrics

### 1. Conversation Tracking

```php
// Get conversation statistics
$stats = AiConversation::forUser($userId)->getSummary();

// Get popular intents
$intents = AiConversation::select('intent')
    ->distinct()
    ->pluck('intent')
    ->filter();
```

### 2. Performance Metrics

```php
// Monitor response times
$startTime = microtime(true);
$result = $this->aiService->processText($message);
$responseTime = microtime(true) - $startTime;

Log::info('AI Response Time: ' . $responseTime . ' seconds');
```

## 🚀 Deployment

### 1. Production Configuration

```env
AI_FLASK_URL=https://your-ai-service.com
AI_TIMEOUT=15
AI_CACHE_TTL=1800
AI_ENABLED=true
AI_FALLBACK_ENABLED=true
```

### 2. Health Checks

```bash
# Add to your deployment script
php artisan ai:test
```

### 3. Monitoring

```bash
# Monitor AI service health
curl -X GET https://your-app.com/api/ai/health
```

## 🔧 Troubleshooting

### Common Issues

1. **AI Service Unavailable**
   - Check Flask service is running
   - Verify network connectivity
   - Check firewall settings

2. **Slow Response Times**
   - Increase timeout in configuration
   - Check AI service performance
   - Optimize cache settings

3. **Memory Issues**
   - Monitor AI service memory usage
   - Implement response streaming
   - Use pagination for large responses

### Debug Commands

```bash
# Test AI service connectivity
curl -X GET http://127.0.0.1:5005/health

# Check Laravel logs
tail -f storage/logs/laravel.log

# Test AI integration
php artisan ai:test --message="test message"
```

## 📈 Performance Optimization

### 1. Caching Strategy

```php
// Cache AI responses
$cacheKey = 'ai_response_' . md5($message . $userId);
$response = Cache::remember($cacheKey, 3600, function () use ($message) {
    return $this->aiService->processText($message);
});
```

### 2. Async Processing

```php
// Process AI requests asynchronously
dispatch(new ProcessAiRequest($message, $userId));
```

### 3. Response Optimization

```php
// Stream large responses
return response()->stream(function () use ($result) {
    echo json_encode($result);
}, 200, ['Content-Type' => 'application/json']);
```

## 🎯 Best Practices

1. **Always validate input** before sending to AI service
2. **Implement proper error handling** and fallback strategies
3. **Cache responses** to improve performance
4. **Monitor service health** and log errors
5. **Use rate limiting** to prevent abuse
6. **Implement security measures** for sensitive medical data
7. **Test thoroughly** before deployment
8. **Document API endpoints** and usage patterns

## 📚 Additional Resources

- [Laravel HTTP Client Documentation](https://laravel.com/docs/http-client)
- [Laravel Caching Documentation](https://laravel.com/docs/cache)
- [Laravel Logging Documentation](https://laravel.com/docs/logging)
- [Flask API Documentation](https://flask.palletsprojects.com/)

---

This integration provides a robust, scalable AI assistant for your medical booking system with proper error handling, caching, and monitoring capabilities.
