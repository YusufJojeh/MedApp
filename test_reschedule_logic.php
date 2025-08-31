<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== RESCHEDULE LOGIC TEST ===\n\n";

// Test 1: Check appointment 182 structure
echo "1. Checking appointment 182 structure...\n";
$appointment = DB::table('appointments')
    ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
    ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
    ->where('appointments.id', 182)
    ->select(
        'appointments.*',
        'doctors.name as doctor_name',
        'doctors.id as doctor_id',
        'specialties.name_en as specialty_name',
        'specialties.id as specialty_id'
    )
    ->first();

if ($appointment) {
    echo "✅ Appointment 182 found:\n";
    echo "   - ID: {$appointment->id}\n";
    echo "   - Patient ID: {$appointment->patient_id}\n";
    echo "   - Doctor: {$appointment->doctor_name} (ID: {$appointment->doctor_id})\n";
    echo "   - Specialty: {$appointment->specialty_name} (ID: {$appointment->specialty_id})\n";
    echo "   - Current Date: {$appointment->appointment_date}\n";
    echo "   - Current Time: {$appointment->appointment_time}\n";
    echo "   - Status: {$appointment->STATUS}\n";
    echo "   - Notes: " . ($appointment->notes ?: 'None') . "\n";
} else {
    echo "❌ Appointment 182 not found\n";
    exit;
}

// Test 2: Check working hours for the doctor
echo "\n2. Checking doctor working hours...\n";
$workingHours = DB::table('working_hours')
    ->where('doctor_id', $appointment->doctor_id)
    ->where('is_available', true)
    ->get();

echo "✅ Doctor has " . count($workingHours) . " working days:\n";
foreach ($workingHours as $wh) {
    $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    echo "   - {$dayNames[$wh->day_of_week]}: {$wh->start_time} - {$wh->end_time}\n";
}

// Test 3: Test reschedule logic for tomorrow
echo "\n3. Testing reschedule logic for tomorrow...\n";
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$dayOfWeek = date('w', strtotime('+1 day'));
$originalTime = $appointment->appointment_time;

echo "   - Target Date: {$tomorrow}\n";
echo "   - Day of Week: {$dayNames[$dayOfWeek]}\n";
echo "   - Original Time: {$originalTime}\n";

// Check if doctor is available tomorrow
$tomorrowWorkingHours = DB::table('working_hours')
    ->where('doctor_id', $appointment->doctor_id)
    ->where('day_of_week', $dayOfWeek)
    ->where('is_available', true)
    ->first();

if ($tomorrowWorkingHours) {
    echo "   - ✅ Doctor is available tomorrow\n";

    // Check if original time is available
    $startTime = Carbon::parse($tomorrowWorkingHours->start_time);
    $endTime = Carbon::parse($tomorrowWorkingHours->end_time);
    $originalTimeCarbon = Carbon::parse($originalTime);

    if ($originalTimeCarbon >= $startTime && $originalTimeCarbon < $endTime) {
        // Check if slot is not booked
        $isBooked = DB::table('appointments')
            ->where('doctor_id', $appointment->doctor_id)
            ->where('appointment_date', $tomorrow)
            ->where('appointment_time', $originalTime)
            ->where('STATUS', '!=', 'cancelled')
            ->exists();

        if (!$isBooked) {
            echo "   - ✅ Original time ({$originalTime}) is available\n";
        } else {
            echo "   - ❌ Original time ({$originalTime}) is already booked\n";
        }
    } else {
        echo "   - ❌ Original time ({$originalTime}) is outside working hours\n";
    }

    // Generate alternative slots
    echo "   - Generating alternative slots...\n";
    $slots = [];
    $currentTime = $startTime->copy();

    while ($currentTime < $endTime) {
        $timeSlot = $currentTime->format('H:i:s');

        // Check if slot is booked
        $isBooked = DB::table('appointments')
            ->where('doctor_id', $appointment->doctor_id)
            ->where('appointment_date', $tomorrow)
            ->where('appointment_time', $timeSlot)
            ->where('STATUS', '!=', 'cancelled')
            ->exists();

        if (!$isBooked) {
            $slots[] = [
                'time' => $timeSlot,
                'formatted_time' => $currentTime->format('g:i A'),
                'available' => true
            ];
        }

        $currentTime->addMinutes(30);
    }

    echo "   - Available slots: " . count($slots) . "\n";
    if (!empty($slots)) {
        echo "   - Sample slots: " . implode(', ', array_slice(array_column($slots, 'formatted_time'), 0, 5)) . "\n";
    }

} else {
    echo "   - ❌ Doctor is not available tomorrow\n";
}

echo "\n=== RESCHEDULE LOGIC SUMMARY ===\n";
echo "Based on the database structure:\n";
echo "1. ✅ Appointments table has: id, patient_id, doctor_id, appointment_date, appointment_time, STATUS, notes\n";
echo "2. ✅ Working hours table has: doctor_id, day_of_week, start_time, end_time, is_available\n";
echo "3. ✅ Reschedule should:\n";
echo "   - Keep the same doctor and specialty\n";
echo "   - Try to keep the same time if available\n";
echo "   - Show 'Not Available' if original time is booked\n";
echo "   - Offer alternative times if original time is not available\n";
echo "   - Update appointment_date and appointment_time\n";
echo "   - Keep the same notes and other fields\n\n";

echo "=== IMPLEMENTATION PLAN ===\n";
echo "1. User selects new date only\n";
echo "2. System checks if original time is available on new date\n";
echo "3. If available: Use original time automatically\n";
echo "4. If not available: Show 'Not Available' + alternative times\n";
echo "5. User can select alternative time or keep trying different dates\n";
echo "6. Submit updates the appointment record\n\n";

echo "Done.\n";
