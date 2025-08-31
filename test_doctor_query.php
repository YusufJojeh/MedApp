<?php

require_once 'vendor/autoload.php';

echo "=== Testing Doctor Query Fix ===\n\n";

// Test 1: Check if doctors exist in database
echo "1. Checking doctors in database...\n";
try {
    $doctors = \Illuminate\Support\Facades\DB::table('doctors')
        ->join('users', 'doctors.user_id', '=', 'users.id')
        ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
        ->where('doctors.is_active', true)
        ->where('users.role', 'doctor')
        ->where('users.status', 'active')
        ->select(
            'doctors.id',
            'doctors.name',
            'doctors.consultation_fee',
            'doctors.rating',
            'doctors.experience_years',
            'specialties.name_en as specialty'
        )
        ->limit(3)
        ->get();

    echo "   Found " . $doctors->count() . " doctors\n";
    foreach ($doctors as $doctor) {
        echo "   - Dr. {$doctor->name} ({$doctor->specialty}) - Rating: {$doctor->rating}/5\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 2: Test AI proxy endpoint
echo "2. Testing AI proxy with 'Find a doctor'...\n";
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode(['text' => 'Find a doctor'])
        ]
    ]);

    $response = file_get_contents('http://127.0.0.1:8000/api/ai/process', false, $context);
    $data = json_decode($response, true);

    if (isset($data['available_doctors'])) {
        echo "   ✅ Available doctors found: " . count($data['available_doctors']) . "\n";
        foreach ($data['available_doctors'] as $doctor) {
            echo "   - Dr. {$doctor['name']} ({$doctor['specialty']}) - Rating: {$doctor['rating']}/5\n";
        }
    } else {
        echo "   ❌ No available doctors in response\n";
        echo "   Intent detected: " . ($data['intent']['intent'] ?? 'unknown') . "\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

echo "=== Test Complete ===\n";
