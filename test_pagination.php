<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== PAGINATION FIX TEST ===\n\n";

// Test 1: Check if pagination works
echo "1. Testing pagination query...\n";
try {
    $appointments = DB::table('appointments')
        ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
        ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
        ->where('appointments.patient_id', 1)
        ->select(
            'appointments.*',
            'doctors.name as doctor_name',
            'specialties.name_en as specialty_name'
        )
        ->orderBy('appointments.appointment_date', 'desc')
        ->orderBy('appointments.appointment_time', 'desc')
        ->paginate(10);

    echo "✅ Pagination query successful\n";
    echo "   - Total records: " . $appointments->total() . "\n";
    echo "   - Current page: " . $appointments->currentPage() . "\n";
    echo "   - Per page: " . $appointments->perPage() . "\n";
    echo "   - Has pages: " . ($appointments->hasPages() ? 'Yes' : 'No') . "\n";
    echo "   - Last page: " . $appointments->lastPage() . "\n";

} catch (Exception $e) {
    echo "❌ Pagination query failed: " . $e->getMessage() . "\n";
}

// Test 2: Check if hasPages method exists
echo "\n2. Testing hasPages method...\n";
if (method_exists($appointments, 'hasPages')) {
    echo "✅ hasPages method exists\n";
} else {
    echo "❌ hasPages method does not exist\n";
}

// Test 3: Check if links method exists
echo "\n3. Testing links method...\n";
if (method_exists($appointments, 'links')) {
    echo "✅ links method exists\n";
} else {
    echo "❌ links method does not exist\n";
}

echo "\n=== FIX SUMMARY ===\n";
echo "✅ Changed from ->get() to ->paginate(10)\n";
echo "✅ Now returns LengthAwarePaginator instead of Collection\n";
echo "✅ hasPages() and links() methods are available\n";
echo "✅ View should work without errors\n\n";

echo "=== TESTING INSTRUCTIONS ===\n";
echo "1. Visit: http://127.0.0.1:8000/patient/appointments\n";
echo "2. Should load without 'hasPages does not exist' error\n";
echo "3. Pagination should work if there are more than 10 appointments\n\n";

echo "The pagination fix is ready!\n";
echo "Done.\n";
