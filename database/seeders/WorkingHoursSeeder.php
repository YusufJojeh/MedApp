<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkingHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workingHours = [
            // Doctor 1 (Dr. Ahmed) - Sunday to Thursday, 9 AM to 5 PM
            ['doctor_id' => 1, 'day_of_week' => 0, 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_available' => true],
            ['doctor_id' => 1, 'day_of_week' => 1, 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_available' => true],
            ['doctor_id' => 1, 'day_of_week' => 2, 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_available' => true],
            ['doctor_id' => 1, 'day_of_week' => 3, 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_available' => true],
            ['doctor_id' => 1, 'day_of_week' => 4, 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_available' => true],

            // Doctor 2 (Dr. Fatma) - Sunday to Thursday, 10 AM to 6 PM
            ['doctor_id' => 2, 'day_of_week' => 0, 'start_time' => '10:00:00', 'end_time' => '18:00:00', 'is_available' => true],
            ['doctor_id' => 2, 'day_of_week' => 1, 'start_time' => '10:00:00', 'end_time' => '18:00:00', 'is_available' => true],
            ['doctor_id' => 2, 'day_of_week' => 2, 'start_time' => '10:00:00', 'end_time' => '18:00:00', 'is_available' => true],
            ['doctor_id' => 2, 'day_of_week' => 3, 'start_time' => '10:00:00', 'end_time' => '18:00:00', 'is_available' => true],
            ['doctor_id' => 2, 'day_of_week' => 4, 'start_time' => '10:00:00', 'end_time' => '18:00:00', 'is_available' => true],

            // Doctor 3 (Dr. Mohamed) - Sunday to Thursday, 8 AM to 4 PM
            ['doctor_id' => 3, 'day_of_week' => 0, 'start_time' => '08:00:00', 'end_time' => '16:00:00', 'is_available' => true],
            ['doctor_id' => 3, 'day_of_week' => 1, 'start_time' => '08:00:00', 'end_time' => '16:00:00', 'is_available' => true],
            ['doctor_id' => 3, 'day_of_week' => 2, 'start_time' => '08:00:00', 'end_time' => '16:00:00', 'is_available' => true],
            ['doctor_id' => 3, 'day_of_week' => 3, 'start_time' => '08:00:00', 'end_time' => '16:00:00', 'is_available' => true],
            ['doctor_id' => 3, 'day_of_week' => 4, 'start_time' => '08:00:00', 'end_time' => '16:00:00', 'is_available' => true],

            // Doctor 4 (Dr. Sara) - Sunday to Thursday, 11 AM to 7 PM
            ['doctor_id' => 4, 'day_of_week' => 0, 'start_time' => '11:00:00', 'end_time' => '19:00:00', 'is_available' => true],
            ['doctor_id' => 4, 'day_of_week' => 1, 'start_time' => '11:00:00', 'end_time' => '19:00:00', 'is_available' => true],
            ['doctor_id' => 4, 'day_of_week' => 2, 'start_time' => '11:00:00', 'end_time' => '19:00:00', 'is_available' => true],
            ['doctor_id' => 4, 'day_of_week' => 3, 'start_time' => '11:00:00', 'end_time' => '19:00:00', 'is_available' => true],
            ['doctor_id' => 4, 'day_of_week' => 4, 'start_time' => '11:00:00', 'end_time' => '19:00:00', 'is_available' => true],

            // Doctor 5 (Dr. Ali) - Sunday to Thursday, 9 AM to 5 PM
            ['doctor_id' => 5, 'day_of_week' => 0, 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_available' => true],
            ['doctor_id' => 5, 'day_of_week' => 1, 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_available' => true],
            ['doctor_id' => 5, 'day_of_week' => 2, 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_available' => true],
            ['doctor_id' => 5, 'day_of_week' => 3, 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_available' => true],
            ['doctor_id' => 5, 'day_of_week' => 4, 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_available' => true],

            // Doctor 6 (Dr. Nora) - Sunday to Thursday, 10 AM to 6 PM
            ['doctor_id' => 6, 'day_of_week' => 0, 'start_time' => '10:00:00', 'end_time' => '18:00:00', 'is_available' => true],
            ['doctor_id' => 6, 'day_of_week' => 1, 'start_time' => '10:00:00', 'end_time' => '18:00:00', 'is_available' => true],
            ['doctor_id' => 6, 'day_of_week' => 2, 'start_time' => '10:00:00', 'end_time' => '18:00:00', 'is_available' => true],
            ['doctor_id' => 6, 'day_of_week' => 3, 'start_time' => '10:00:00', 'end_time' => '18:00:00', 'is_available' => true],
            ['doctor_id' => 6, 'day_of_week' => 4, 'start_time' => '10:00:00', 'end_time' => '18:00:00', 'is_available' => true],
        ];

        foreach ($workingHours as $hours) {
            DB::table('working_hours')->insert($hours);
        }
    }
}
