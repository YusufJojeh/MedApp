<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    /**
     * Display the doctor's schedule
     */
    public function index()
    {
        $doctor = Auth::user()->doctor;
        
        $schedules = DB::table('doctor_schedules')
            ->where('doctor_id', $doctor->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('doctor.schedule.index', compact('schedules'));
    }

    /**
     * Store a new schedule
     */
    public function store(Request $request)
    {
        $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'boolean'
        ]);

        $doctor = Auth::user()->doctor;

        DB::table('doctor_schedules')->insert([
            'doctor_id' => $doctor->id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => $request->boolean('is_available', true),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('doctor.schedule.index')
            ->with('success', 'Schedule added successfully.');
    }

    /**
     * Update a schedule
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'boolean'
        ]);

        $doctor = Auth::user()->doctor;

        DB::table('doctor_schedules')
            ->where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->update([
                'day_of_week' => $request->day_of_week,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'is_available' => $request->boolean('is_available', true),
                'updated_at' => now()
            ]);

        return redirect()->route('doctor.schedule.index')
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Delete a schedule
     */
    public function destroy($id)
    {
        $doctor = Auth::user()->doctor;

        DB::table('doctor_schedules')
            ->where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->delete();

        return redirect()->route('doctor.schedule.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}
