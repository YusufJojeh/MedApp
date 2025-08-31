<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DoctorController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of doctors
     */
    public function index(Request $request)
    {
        $query = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->where('doctors.is_active', true)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name',
                'specialties.name_ar as specialty_name_ar',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone'
            );

        // Apply filters
        if ($request->filled('specialty_id')) {
            $query->where('doctors.specialty_id', $request->specialty_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('doctors.name', 'like', '%' . $search . '%')
                  ->orWhere('users.first_name', 'like', '%' . $search . '%')
                  ->orWhere('users.last_name', 'like', '%' . $search . '%')
                  ->orWhere('specialties.name_en', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('min_rating')) {
            $query->where('doctors.rating', '>=', $request->min_rating);
        }

        if ($request->filled('max_fee')) {
            $query->where('doctors.consultation_fee', '<=', $request->max_fee);
        }

        if ($request->filled('experience_years')) {
            $query->where('doctors.experience_years', '>=', $request->experience_years);
        }

        if ($request->filled('is_emergency_available')) {
            $query->where('doctors.is_emergency_available', $request->is_emergency_available);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'rating');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'name':
                $query->orderBy('doctors.name', $sortOrder);
                break;
            case 'rating':
                $query->orderBy('doctors.rating', $sortOrder);
                break;
            case 'consultation_fee':
                $query->orderBy('doctors.consultation_fee', $sortOrder);
                break;
            case 'experience_years':
                $query->orderBy('doctors.experience_years', $sortOrder);
                break;
            default:
                $query->orderBy('doctors.rating', 'desc');
        }

        $doctors = $query->paginate(12);
        $specialties = DB::table('specialties')->select('id', 'name_en', 'name_ar')->get();

        return view('doctors.index', compact('doctors', 'specialties'));
    }

    /**
     * Show the form for creating a new doctor
     */
    public function create()
    {
        $specialties = DB::table('specialties')->select('id', 'name_en', 'name_ar')->get();
        $users = DB::table('users')->where('role', 'doctor')->get();

        return view('doctors.create', compact('specialties', 'users'));
    }

    /**
     * Store a newly created doctor
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'specialty_id' => 'required|exists:specialties,id',
            'name' => 'required|string|max:255',
            'consultation_fee' => 'required|numeric|min:0',
            'experience_years' => 'required|integer|min:0|max:50',
            'education' => 'nullable|string|max:255',
            'languages' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_emergency_available' => 'boolean',
            'profile_image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user is already a doctor
        $existingDoctor = DB::table('doctors')->where('user_id', $request->user_id)->first();
        if ($existingDoctor) {
            return response()->json(['error' => 'User is already registered as a doctor'], 400);
        }

        DB::beginTransaction();
        try {
            $doctorId = DB::table('doctors')->insertGetId([
                'user_id' => $request->user_id,
                'specialty_id' => $request->specialty_id,
                'name' => $request->name,
                'consultation_fee' => $request->consultation_fee,
                'experience_years' => $request->experience_years,
                'education' => $request->education,
                'languages' => $request->languages,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'is_emergency_available' => $request->is_emergency_available ?? false,
                'profile_image' => $request->profile_image,
                'rating' => 0,
                'total_reviews' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create wallet for doctor
            DB::table('wallets')->insert([
                'user_id' => $request->user_id,
                'balance' => 0.00,
                'currency' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create default working hours
            for ($day = 0; $day < 7; $day++) {
                DB::table('working_hours')->insert([
                    'doctor_id' => $doctorId,
                    'day_of_week' => $day,
                    'is_available' => $day >= 1 && $day <= 5, // Monday to Friday
                    'start_time' => $day >= 1 && $day <= 5 ? '09:00:00' : null,
                    'end_time' => $day >= 1 && $day <= 5 ? '17:00:00' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Doctor created successfully',
                'doctor_id' => $doctorId
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified doctor
     */
    public function show($id)
    {
        $doctor = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->where('doctors.id', $id)
            ->where('doctors.is_active', true)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name',
                'specialties.name_ar as specialty_name_ar',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone'
            )
            ->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        // Get working hours
        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $id)
            ->orderBy('day_of_week')
            ->get();

        // Get recent appointments
        $recentAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $id)
            ->where('appointments.STATUS', 'completed')
            ->select('appointments.*', 'patients.NAME as patient_name')
            ->orderBy('appointments.appointment_date', 'desc')
            ->limit(5)
            ->get();

        // Get doctor statistics
        $stats = $this->getDoctorStats($id);

        return view('doctors.show', compact('doctor', 'workingHours', 'recentAppointments', 'stats'));
    }

    /**
     * Show the form for editing the specified doctor
     */
    public function edit($id)
    {
        $doctor = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->where('doctors.id', $id)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone'
            )
            ->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        $specialties = DB::table('specialties')->select('id', 'name_en', 'name_ar')->get();
        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $id)
            ->orderBy('day_of_week')
            ->get();

        return view('doctors.edit', compact('doctor', 'specialties', 'workingHours'));
    }

    /**
     * Update the specified doctor
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'specialty_id' => 'required|exists:specialties,id',
            'name' => 'required|string|max:255',
            'consultation_fee' => 'required|numeric|min:0',
            'experience_years' => 'required|integer|min:0|max:50',
            'education' => 'nullable|string|max:255',
            'languages' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_emergency_available' => 'boolean',
            'profile_image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctor = DB::table('doctors')->where('id', $id)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        DB::beginTransaction();
        try {
            DB::table('doctors')->where('id', $id)->update([
                'specialty_id' => $request->specialty_id,
                'name' => $request->name,
                'consultation_fee' => $request->consultation_fee,
                'experience_years' => $request->experience_years,
                'education' => $request->education,
                'languages' => $request->languages,
                'description' => $request->description,
                'is_active' => $request->is_active ?? $doctor->is_active,
                'is_emergency_available' => $request->is_emergency_available ?? $doctor->is_emergency_available,
                'profile_image' => $request->profile_image ?? $doctor->profile_image,
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Doctor updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified doctor
     */
    public function destroy($id)
    {
        $doctor = DB::table('doctors')->where('id', $id)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        // Check if doctor has active appointments
        $activeAppointments = DB::table('appointments')
            ->where('doctor_id', $id)
            ->whereIn('STATUS', ['scheduled', 'confirmed'])
            ->count();

        if ($activeAppointments > 0) {
            return response()->json(['error' => 'Cannot delete doctor with active appointments'], 400);
        }

        DB::beginTransaction();
        try {
            // Deactivate doctor instead of deleting
            DB::table('doctors')->where('id', $id)->update([
                'is_active' => false,
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Doctor deactivated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deactivating doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get doctor's working hours
     */
    public function getWorkingHours($id)
    {
        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $id)
            ->orderBy('day_of_week')
            ->get();

        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday'
        ];

        $formattedHours = [];
        foreach ($workingHours as $hour) {
            $formattedHours[] = [
                'day' => $days[$hour->day_of_week],
                'day_number' => $hour->day_of_week,
                'is_available' => $hour->is_available,
                'start_time' => $hour->start_time,
                'end_time' => $hour->end_time,
            ];
        }

        return response()->json($formattedHours);
    }

    /**
     * Update doctor's working hours
     */
    public function updateWorkingHours(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'working_hours' => 'required|array',
            'working_hours.*.day_of_week' => 'required|integer|between:0,6',
            'working_hours.*.is_available' => 'required|boolean',
            'working_hours.*.start_time' => 'required_if:working_hours.*.is_available,true|date_format:H:i:s',
            'working_hours.*.end_time' => 'required_if:working_hours.*.is_available,true|date_format:H:i:s|after:working_hours.*.start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctor = DB::table('doctors')->where('id', $id)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        DB::beginTransaction();
        try {
            foreach ($request->working_hours as $workingHour) {
                DB::table('working_hours')
                    ->where('doctor_id', $id)
                    ->where('day_of_week', $workingHour['day_of_week'])
                    ->update([
                        'is_available' => $workingHour['is_available'],
                        'start_time' => $workingHour['is_available'] ? $workingHour['start_time'] : null,
                        'end_time' => $workingHour['is_available'] ? $workingHour['end_time'] : null,
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Working hours updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating working hours: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get doctor's reviews and ratings
     */
    public function getReviews($id)
    {
        $reviews = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.doctor_id', $id)
            ->where('appointments.STATUS', 'completed')
            ->whereNotNull('appointments.rating')
            ->select(
                'appointments.rating',
                'appointments.review',
                'appointments.appointment_date',
                'patients.NAME as patient_name'
            )
            ->orderBy('appointments.appointment_date', 'desc')
            ->paginate(10);

        return response()->json($reviews);
    }

    /**
     * Search doctors
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = $request->query;

        $doctors = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->where('doctors.is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('doctors.name', 'like', '%' . $query . '%')
                  ->orWhere('users.first_name', 'like', '%' . $query . '%')
                  ->orWhere('users.last_name', 'like', '%' . $query . '%')
                  ->orWhere('specialties.name_en', 'like', '%' . $query . '%')
                  ->orWhere('specialties.name_ar', 'like', '%' . $query . '%');
            })
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name',
                'users.first_name',
                'users.last_name'
            )
            ->orderBy('doctors.rating', 'desc')
            ->limit(10)
            ->get();

        return response()->json($doctors);
    }

    /**
     * Get doctor statistics
     */
    public function getStats($id)
    {
        $stats = $this->getDoctorStats($id);
        return response()->json($stats);
    }

    /**
     * Export doctors data
     */
    public function export(Request $request)
    {
        $query = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone'
            );

        // Apply filters
        if ($request->filled('specialty_id')) {
            $query->where('doctors.specialty_id', $request->specialty_id);
        }

        if ($request->filled('is_active')) {
            $query->where('doctors.is_active', $request->is_active);
        }

        $doctors = $query->orderBy('doctors.name')->get();

        return response()->json($doctors);
    }

    /**
     * Get doctor statistics
     */
    private function getDoctorStats($doctorId)
    {
        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $totalAppointments = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->count();

        $completedAppointments = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->where('STATUS', 'completed')
            ->count();

        $thisMonthAppointments = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->whereMonth('appointment_date', $currentDate->month)
            ->whereYear('appointment_date', $currentDate->year)
            ->count();

        $lastMonthAppointments = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->whereMonth('appointment_date', $lastMonth->month)
            ->whereYear('appointment_date', $lastMonth->year)
            ->count();

        $totalRevenue = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->where('appointments.doctor_id', $doctorId)
            ->where('payments.STATUS', 'succeeded')
            ->sum('payments.amount');

        $thisMonthRevenue = DB::table('payments')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->where('appointments.doctor_id', $doctorId)
            ->where('payments.STATUS', 'succeeded')
            ->whereMonth('payments.created_at', $currentDate->month)
            ->whereYear('payments.created_at', $currentDate->year)
            ->sum('payments.amount');

        $averageRating = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->where('STATUS', 'completed')
            ->whereNotNull('rating')
            ->avg('rating');

        return [
            'total_appointments' => $totalAppointments,
            'completed_appointments' => $completedAppointments,
            'this_month_appointments' => $thisMonthAppointments,
            'last_month_appointments' => $lastMonthAppointments,
            'total_revenue' => $totalRevenue,
            'this_month_revenue' => $thisMonthRevenue,
            'average_rating' => round($averageRating, 1),
            'completion_rate' => $totalAppointments > 0 ? round(($completedAppointments / $totalAppointments) * 100, 1) : 0,
        ];
    }
}
