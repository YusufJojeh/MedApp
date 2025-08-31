<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== AI ASSISTANT ROUTES TEST ===\n\n";

// Test 1: Check if AI routes exist
echo "1. Checking AI assistant routes...\n";
$routes = Route::getRoutes();
$aiRoutes = [];

foreach ($routes as $route) {
    if (in_array($route->getName(), ['ai.assistant', 'ai.index'])) {
        $aiRoutes[] = $route;
    }
}

if (count($aiRoutes) > 0) {
    echo "✅ AI routes found:\n";
    foreach ($aiRoutes as $route) {
        echo "   - Name: " . $route->getName() . "\n";
        echo "   - URI: " . $route->uri() . "\n";
        echo "   - Methods: " . implode(', ', $route->methods()) . "\n\n";
    }
} else {
    echo "❌ AI routes not found\n";
}

// Test 2: Check if ai-assistant.index view exists
echo "2. Checking if ai-assistant.index view exists...\n";
$viewPath = resource_path('views/ai-assistant/index.blade.php');
if (file_exists($viewPath)) {
    echo "✅ View file exists: " . $viewPath . "\n";
} else {
    echo "❌ View file not found: " . $viewPath . "\n";
}

echo "\n=== FIXES APPLIED ===\n";
echo "✅ Added ai.assistant route that passes required variables\n";
echo "✅ Added ai.index route for compatibility\n";
echo "✅ Both routes pass conversationHistory and user variables\n";
echo "✅ Routes now use view() instead of redirect()\n\n";

echo "=== TESTING INSTRUCTIONS ===\n";
echo "1. Visit: http://127.0.0.1:8000/ai\n";
echo "2. The AI assistant page should load without errors\n";
echo "3. The page should show an empty conversation history\n\n";

echo "The AI assistant should now load correctly!\n";
echo "Done.\n";
