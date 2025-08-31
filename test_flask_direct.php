<?php

echo "=== Testing Flask Service Directly ===\n\n";

// Test Flask service directly
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode(['text' => 'Book an appointment'])
    ]
]);

try {
    $response = file_get_contents('http://127.0.0.1:5006/process', false, $context);
    $data = json_decode($response, true);

    echo "Flask Response:\n";
    echo "Intent: " . ($data['intent']['intent'] ?? 'unknown') . "\n";
    echo "Confidence: " . ($data['intent']['confidence'] ?? 'unknown') . "\n";
    echo "Specialty Hint: " . ($data['intent']['specialty_hint'] ?? 'none') . "\n";

    if (isset($data['doctors'])) {
        echo "Doctors: " . ($data['doctors'] ? 'Yes' : 'No') . "\n";
    }

    if (isset($data['health_tips'])) {
        echo "Health Tips: Yes\n";
    }

    echo "\nFull Response:\n";
    print_r($data);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Laravel Proxy ===\n\n";

// Test Laravel proxy
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode(['text' => 'Book an appointment'])
    ]
]);

try {
    $response = file_get_contents('http://127.0.0.1:8000/api/ai/process', false, $context);
    $data = json_decode($response, true);

    echo "Laravel Proxy Response:\n";
    echo "Intent: " . ($data['intent']['intent'] ?? 'unknown') . "\n";
    echo "Confidence: " . ($data['intent']['confidence'] ?? 'unknown') . "\n";
    echo "Response Message: " . ($data['response_message'] ?? 'Not set') . "\n";

    if (isset($data['formatted_doctors'])) {
        echo "Formatted Doctors: " . count($data['formatted_doctors']) . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
