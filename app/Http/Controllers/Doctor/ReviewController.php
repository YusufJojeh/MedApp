<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    /**
     * Display doctor's reviews
     */
    public function index()
    {
        $doctor = Auth::user()->doctor;
        
        $reviews = DB::table('doctor_reviews')
            ->join('patients', 'doctor_reviews.patient_id', '=', 'patients.id')
            ->join('users', 'patients.user_id', '=', 'users.id')
            ->where('doctor_reviews.doctor_id', $doctor->id)
            ->select(
                'doctor_reviews.*',
                'users.first_name',
                'users.last_name',
                'users.email'
            )
            ->orderBy('doctor_reviews.created_at', 'desc')
            ->paginate(10);

        return view('doctor.reviews.index', compact('reviews'));
    }

    /**
     * Show a specific review
     */
    public function show($id)
    {
        $doctor = Auth::user()->doctor;
        
        $review = DB::table('doctor_reviews')
            ->join('patients', 'doctor_reviews.patient_id', '=', 'patients.id')
            ->join('users', 'patients.user_id', '=', 'users.id')
            ->where('doctor_reviews.id', $id)
            ->where('doctor_reviews.doctor_id', $doctor->id)
            ->select(
                'doctor_reviews.*',
                'users.first_name',
                'users.last_name',
                'users.email'
            )
            ->first();

        if (!$review) {
            abort(404);
        }

        return view('doctor.reviews.show', compact('review'));
    }

    /**
     * Get review statistics
     */
    public function getStats()
    {
        $doctor = Auth::user()->doctor;
        
        $stats = DB::table('doctor_reviews')
            ->where('doctor_id', $doctor->id)
            ->selectRaw('
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
            ')
            ->first();

        return response()->json($stats);
    }
}
