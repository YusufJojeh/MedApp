<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
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
        $this->middleware('patient');
    }

    /**
     * Display a listing of doctors
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        $query = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->leftJoin('doctor_favorites', function($join) use ($patient) {
                $join->on('doctors.id', '=', 'doctor_favorites.doctor_id')
                     ->where('doctor_favorites.patient_id', '=', $patient->id);
            })
            ->where('doctors.is_active', true)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name',
                'specialties.name_ar as specialty_name_ar',
                DB::raw('CASE WHEN doctor_favorites.id IS NOT NULL THEN 1 ELSE 0 END as is_favorite'),
                DB::raw('doctors.rating as average_rating'),
                DB::raw('doctors.is_active as is_available')
            );

        // Apply filters
        if ($request->filled('specialty_id')) {
            $query->where('doctors.specialty_id', $request->specialty_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('doctors.name', 'like', "%{$search}%")
                  ->orWhere('doctors.education', 'like', "%{$search}%")
                  ->orWhere('doctors.languages', 'like', "%{$search}%")
                  ->orWhere('specialties.name_en', 'like', "%{$search}%");
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
            case 'fee':
                $query->orderBy('doctors.consultation_fee', $sortOrder);
                break;
            case 'experience':
                $query->orderBy('doctors.experience_years', $sortOrder);
                break;
            default:
                $query->orderBy('doctors.rating', 'desc');
        }

        $doctors = $query->paginate(12);

        // Get specialties for filter
        $specialties = DB::table('specialties')
            ->select('id', 'name_en', 'name_ar')
            ->get();

        return view('patient.doctors.index', compact('doctors', 'specialties'));
    }

    /**
     * Display the specified doctor
     */
    public function show($id)
    {
        $doctor = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('doctors.id', $id)
            ->where('doctors.is_active', true)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name',
                'specialties.name_ar as specialty_name_ar'
            )
            ->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        // Get working hours
        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $doctor->id)
            ->orderBy('day_of_week')
            ->get();

        // Get doctor's ratings and reviews
        $ratings = DB::table('appointment_ratings')
            ->join('patients', 'appointment_ratings.patient_id', '=', 'patients.id')
            ->where('appointment_ratings.doctor_id', $doctor->id)
            ->select(
                'appointment_ratings.*',
                'patients.NAME as patient_name'
            )
            ->orderBy('appointment_ratings.created_at', 'desc')
            ->limit(10)
            ->get();

        // Get average rating
        $avgRating = DB::table('appointment_ratings')
            ->where('doctor_id', $doctor->id)
            ->avg('rating');

        // Get rating distribution
        $ratingDistribution = DB::table('appointment_ratings')
            ->where('doctor_id', $doctor->id)
            ->select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        // Get patient's appointment history with this doctor
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        $patientHistory = null;
        if ($patient) {
            $patientHistory = DB::table('appointments')
                ->where('doctor_id', $doctor->id)
                ->where('patient_id', $patient->id)
                ->orderBy('appointment_date', 'desc')
                ->limit(5)
                ->get();
        }

        return view('patient.doctors.show', compact(
            'doctor',
            'workingHours',
            'ratings',
            'avgRating',
            'ratingDistribution',
            'patientHistory'
        ));
    }

    /**
     * Search doctors
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
            'specialty_id' => 'nullable|exists:specialties,id',
            'date' => 'nullable|date|after_or_equal:today'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('doctors.is_active', true)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name'
            );

        // Apply search
        $searchQuery = $request->query;
        $query->where(function ($q) use ($searchQuery) {
            $q->where('doctors.name', 'like', "%{$searchQuery}%")
              ->orWhere('doctors.education', 'like', "%{$searchQuery}%")
              ->orWhere('doctors.languages', 'like', "%{$searchQuery}%")
              ->orWhere('specialties.name_en', 'like', "%{$searchQuery}%");
        });

        // Apply specialty filter
        if ($request->filled('specialty_id')) {
            $query->where('doctors.specialty_id', $request->specialty_id);
        }

        // Apply availability filter
        if ($request->filled('date')) {
            $date = $request->date;
            $dayOfWeek = Carbon::parse($date)->dayOfWeek;

            $query->whereExists(function ($subQuery) use ($dayOfWeek) {
                $subQuery->select(DB::raw(1))
                    ->from('working_hours')
                    ->whereColumn('working_hours.doctor_id', 'doctors.id')
                    ->where('working_hours.day_of_week', $dayOfWeek)
                    ->where('working_hours.is_available', true);
            });
        }

        $doctors = $query->orderBy('doctors.rating', 'desc')
            ->orderBy('doctors.name')
            ->limit(20)
            ->get();

        return response()->json($doctors);
    }

    /**
     * Get available doctors for a specific date
     */
    public function getAvailableDoctors(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
            'specialty_id' => 'nullable|exists:specialties,id',
            'time' => 'nullable|date_format:H:i:s'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $date = $request->date;
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $query = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->join('working_hours', 'doctors.id', '=', 'working_hours.doctor_id')
            ->where('doctors.is_active', true)
            ->where('working_hours.day_of_week', $dayOfWeek)
            ->where('working_hours.is_available', true)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name',
                'working_hours.start_time',
                'working_hours.end_time'
            );

        // Apply specialty filter
        if ($request->filled('specialty_id')) {
            $query->where('doctors.specialty_id', $request->specialty_id);
        }

        // Apply time filter
        if ($request->filled('time')) {
            $time = $request->time;
            $query->where('working_hours.start_time', '<=', $time)
                  ->where('working_hours.end_time', '>', $time);
        }

        $doctors = $query->orderBy('doctors.rating', 'desc')
            ->orderBy('doctors.name')
            ->get();

        // Filter out doctors with conflicting appointments
        if ($request->filled('time')) {
            $time = $request->time;
            $doctors = $doctors->filter(function ($doctor) use ($date, $time) {
                $conflict = DB::table('appointments')
                    ->where('doctor_id', $doctor->id)
                    ->where('appointment_date', $date)
                    ->where('appointment_time', $time)
                    ->where('STATUS', '!=', 'cancelled')
                    ->exists();

                return !$conflict;
            });
        }

        return response()->json($doctors->values());
    }

    /**
     * Get doctor's working hours
     */
    public function getWorkingHours($doctorId)
    {
        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $doctorId)
            ->orderBy('day_of_week')
            ->get();

        return response()->json($workingHours);
    }

    /**
     * Get doctor's available time slots
     */
    public function getAvailableSlots(Request $request, $doctorId)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $date = $request->date;
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        // Get doctor's working hours for this day
        $workingHours = DB::table('working_hours')
            ->where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$workingHours) {
            return response()->json(['message' => 'Doctor is not available on this day']);
        }

        // Get booked appointments for this date
        $bookedSlots = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('STATUS', '!=', 'cancelled')
            ->pluck('appointment_time')
            ->toArray();

        // Generate available time slots (30-minute intervals)
        $startTime = Carbon::parse($workingHours->start_time);
        $endTime = Carbon::parse($workingHours->end_time);
        $availableSlots = [];

        while ($startTime < $endTime) {
            $timeSlot = $startTime->format('H:i:s');
            if (!in_array($timeSlot, $bookedSlots)) {
                $availableSlots[] = [
                    'time' => $timeSlot,
                    'formatted_time' => $startTime->format('g:i A')
                ];
            }
            $startTime->addMinutes(30);
        }

        return response()->json($availableSlots);
    }

    /**
     * Get doctor's ratings and reviews
     */
    public function getRatings($doctorId)
    {
        $ratings = DB::table('appointment_ratings')
            ->join('patients', 'appointment_ratings.patient_id', '=', 'patients.id')
            ->where('appointment_ratings.doctor_id', $doctorId)
            ->select(
                'appointment_ratings.*',
                'patients.NAME as patient_name'
            )
            ->orderBy('appointment_ratings.created_at', 'desc')
            ->paginate(10);

        return response()->json($ratings);
    }

    /**
     * Get doctor's statistics
     */
    public function getStats($doctorId)
    {
        $doctor = DB::table('doctors')
            ->where('id', $doctorId)
            ->where('is_active', true)
            ->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        $currentDate = Carbon::now();

        $stats = [
            'total_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctorId)
                ->count(),
            'completed_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctorId)
                ->where('STATUS', 'completed')
                ->count(),
            'total_patients' => DB::table('appointments')
                ->where('doctor_id', $doctorId)
                ->distinct('patient_id')
                ->count('patient_id'),
            'average_rating' => DB::table('appointment_ratings')
                ->where('doctor_id', $doctorId)
                ->avg('rating'),
            'total_reviews' => DB::table('appointment_ratings')
                ->where('doctor_id', $doctorId)
                ->count(),
            'this_month_appointments' => DB::table('appointments')
                ->where('doctor_id', $doctorId)
                ->whereMonth('appointment_date', $currentDate->month)
                ->whereYear('appointment_date', $currentDate->year)
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get top rated doctors
     */
    public function getTopRated()
    {
        $doctors = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('doctors.is_active', true)
            ->where('doctors.rating', '>=', 4.0)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name'
            )
            ->orderBy('doctors.rating', 'desc')
            ->orderBy('doctors.total_reviews', 'desc')
            ->limit(10)
            ->get();

        return response()->json($doctors);
    }

    /**
     * Get doctors by specialty
     */
    public function getBySpecialty($specialtyId)
    {
        $doctors = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('doctors.specialty_id', $specialtyId)
            ->where('doctors.is_active', true)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name'
            )
            ->orderBy('doctors.rating', 'desc')
            ->orderBy('doctors.name')
            ->get();

        return response()->json($doctors);
    }

    /**
     * Get favorite doctors (based on appointment history)
     */
    public function getFavoriteDoctors()
    {
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $favoriteDoctors = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $patient->id)
            ->where('doctors.is_active', true)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name',
                DB::raw('COUNT(appointments.id) as appointment_count')
            )
            ->groupBy('doctors.id', 'doctors.name', 'doctors.specialty_id', 'doctors.consultation_fee', 'doctors.rating', 'specialties.name_en')
            ->orderBy('appointment_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json($favoriteDoctors);
    }

    /**
     * Get recently visited doctors
     */
    public function getRecentlyVisited()
    {
        $patientId = Auth::user()->id;
        $patient = DB::table('patients')->where('user_id', $patientId)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $recentDoctors = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('appointments.patient_id', $patient->id)
            ->where('doctors.is_active', true)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name',
                DB::raw('MAX(appointments.appointment_date) as last_visit')
            )
            ->groupBy('doctors.id', 'doctors.name', 'doctors.specialty_id', 'doctors.consultation_fee', 'doctors.rating', 'specialties.name_en')
            ->orderBy('last_visit', 'desc')
            ->limit(10)
            ->get();

        return response()->json($recentDoctors);
    }

    /**
     * Toggle favorite status for a doctor
     */
    public function toggleFavorite($doctorId)
    {
        $user = Auth::user();
        $patient = DB::table('patients')->where('user_id', $user->id)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        // Check if doctor exists and is active
        $doctor = DB::table('doctors')
            ->where('id', $doctorId)
            ->where('is_active', true)
            ->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        // Check if already favorited
        $existingFavorite = DB::table('doctor_favorites')
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctorId)
            ->first();

        if ($existingFavorite) {
            // Remove from favorites
            DB::table('doctor_favorites')
                ->where('patient_id', $patient->id)
                ->where('doctor_id', $doctorId)
                ->delete();

            return response()->json([
                'success' => true,
                'is_favorite' => false,
                'message' => 'Doctor removed from favorites'
            ]);
        } else {
            // Add to favorites
            DB::table('doctor_favorites')->insert([
                'patient_id' => $patient->id,
                'doctor_id' => $doctorId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'is_favorite' => true,
                'message' => 'Doctor added to favorites'
            ]);
        }
    }
}
