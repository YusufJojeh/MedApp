<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Test configuration
$baseUrl = 'http://127.0.0.1:8000';
$testUser = [
    'email' => 'test@example.com',
    'password' => 'password'
];

echo "=== AI Booking Integration Test ===\n\n";

// Test 1: Check feature status
echo "1. Testing feature status...\n";
try {
    $response = Http::get("$baseUrl/api/ai-booking/feature-status");
    $data = $response->json();

    if ($response->successful()) {
        echo "✅ Feature status retrieved successfully\n";
        echo "   - AI Booking Enabled: " . ($data['features']['ai_booking_enabled'] ? 'Yes' : 'No') . "\n";
        echo "   - Wallet Integration: " . ($data['features']['wallet_integration_enabled'] ? 'Yes' : 'No') . "\n";
        echo "   - AI Service URL: " . $data['features']['ai_service_url'] . "\n\n";
    } else {
        echo "❌ Failed to get feature status: " . $response->status() . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Error testing feature status: " . $e->getMessage() . "\n\n";
}

// Test 2: Test AI intent processing (without auth)
echo "2. Testing AI intent processing (without auth)...\n";
try {
    $response = Http::post("$baseUrl/api/ai-booking/process-intent", [
        'message' => 'I want to book an appointment with a cardiologist'
    ]);

    if ($response->status() === 401) {
        echo "✅ Authentication required (expected)\n\n";
    } else {
        echo "⚠️ Unexpected response: " . $response->status() . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Error testing AI intent: " . $e->getMessage() . "\n\n";
}

// Test 3: Test Flask service directly
echo "3. Testing Flask service directly...\n";
try {
    $response = Http::timeout(5)->post('http://127.0.0.1:5005/process', [
        'text' => 'I want to book an appointment with a cardiologist'
    ]);

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Flask service responding\n";
        echo "   - Intent: " . ($data['intent']['intent'] ?? 'unknown') . "\n";
        echo "   - Confidence: " . ($data['intent']['confidence'] ?? 'unknown') . "\n";
        echo "   - Specialty Hint: " . ($data['intent']['specialty_hint'] ?? 'none') . "\n\n";
    } else {
        echo "❌ Flask service not responding: " . $response->status() . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Flask service error: " . $e->getMessage() . "\n\n";
}

// Test 4: Test AI proxy endpoint
echo "4. Testing AI proxy endpoint...\n";
try {
    $response = Http::post("$baseUrl/api/ai/process", [
        'text' => 'I want to book an appointment with a cardiologist'
    ]);

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ AI proxy working\n";
        echo "   - Intent: " . ($data['intent']['intent'] ?? 'unknown') . "\n";
        echo "   - Has available doctors: " . (isset($data['available_doctors']) ? 'Yes' : 'No') . "\n\n";
    } else {
        echo "❌ AI proxy failed: " . $response->status() . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ AI proxy error: " . $e->getMessage() . "\n\n";
}

echo "=== Test Summary ===\n";
echo "To enable AI booking, set these environment variables:\n";
echo "AI_BOOKING_ENABLED=true\n";
echo "AI_WALLET_INTEGRATION=true\n";
echo "AI_FLASK_URL=http://127.0.0.1:5005\n\n";

echo "Test the booking flow with:\n";
echo "1. POST /api/ai-booking/process-intent (with auth)\n";
echo "2. GET /api/ai-booking/doctors?specialty=cardiology\n";
echo "3. POST /api/ai-booking/check-availability\n";
echo "4. POST /api/ai-booking/book-appointment\n\n";

echo "Frontend integration:\n";
echo "- Use /api/ai/process for general AI chat\n";
echo "- Use /api/ai-booking/* for booking-specific actions\n";
echo "- Check feature status before showing booking options\n";
