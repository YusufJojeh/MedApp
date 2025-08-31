<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Display the home page
     */
    public function index()
    {
        // Get featured doctors
        $featuredDoctors = DB::table('doctors')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->where('doctors.is_active', true)
            ->where('doctors.rating', '>=', 4.0)
            ->select(
                'doctors.*',
                'specialties.name_en as specialty_name'
            )
            ->orderBy('doctors.rating', 'desc')
            ->limit(6)
            ->get();

        // Get specialties
        $specialties = DB::table('specialties')
            ->select('id', 'name_en', 'name_ar', 'description')
            ->limit(8)
            ->get();

        // Get statistics
        $stats = $this->getPublicStats();

        // Get testimonials
        $testimonials = $this->getTestimonials();

        return view('home', compact('featuredDoctors', 'specialties', 'stats', 'testimonials'));
    }

    /**
     * Display about page
     */
    public function about()
    {
        $stats = $this->getPublicStats();
        $team = $this->getTeamMembers();

        return view('about', compact('stats', 'team'));
    }

    /**
     * Display services page
     */
    public function services()
    {
        $specialties = DB::table('specialties')
            ->select('id', 'name_en', 'name_ar', 'description')
            ->get();

        $services = $this->getServices();

        return view('services', compact('specialties', 'services'));
    }

    /**
     * Display contact page
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Handle contact form submission
     */
    public function contactSubmit(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Save contact message
            DB::table('contact_messages')->insert([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => 'unread',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message. We will get back to you soon!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display FAQ page
     */
    public function faq()
    {
        $faqs = $this->getFAQs();

        return view('faq', compact('faqs'));
    }

    /**
     * Display privacy policy page
     */
    public function privacy()
    {
        return view('privacy');
    }

    /**
     * Display terms of service page
     */
    public function terms()
    {
        return view('terms');
    }

    /**
     * Search doctors from home page
     */
    public function search(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'query' => 'required|string|min:2',
            'specialty_id' => 'nullable|exists:specialties,id',
            'location' => 'nullable|string|max:255',
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

        // Apply search filters
        if ($request->filled('query')) {
            $searchQuery = $request->query;
            $query->where(function ($q) use ($searchQuery) {
                $q->where('doctors.name', 'like', '%' . $searchQuery . '%')
                  ->orWhere('specialties.name_en', 'like', '%' . $searchQuery . '%')
                  ->orWhere('doctors.description', 'like', '%' . $searchQuery . '%');
            });
        }

        if ($request->filled('specialty_id')) {
            $query->where('doctors.specialty_id', $request->specialty_id);
        }

        $doctors = $query->orderBy('doctors.rating', 'desc')
            ->paginate(12);

        $specialties = DB::table('specialties')->select('id', 'name_en')->get();

        return view('search-results', compact('doctors', 'specialties'));
    }

    /**
     * Get available specialties
     */
    public function getSpecialties()
    {
        $specialties = DB::table('specialties')
            ->select('id', 'name_en', 'name_ar', 'description')
            ->get();

        return response()->json($specialties);
    }

    /**
     * Get featured doctors
     */
    public function getFeaturedDoctors()
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
            ->limit(8)
            ->get();

        return response()->json($doctors);
    }

    /**
     * Get public statistics
     */
    public function getPublicStats()
    {
        $stats = [
            'total_doctors' => DB::table('doctors')->where('is_active', true)->count(),
            'total_patients' => DB::table('patients')->count(),
            'total_appointments' => DB::table('appointments')->where('STATUS', 'completed')->count(),
            'total_specialties' => DB::table('specialties')->count(),
        ];

        return $stats;
    }

    /**
     * Get testimonials
     */
    private function getTestimonials()
    {
        // This would typically come from a testimonials table
        // For now, return sample testimonials
        return [
            [
                'name' => 'Sarah Johnson',
                'rating' => 5,
                'comment' => 'Excellent service! The doctors are very professional and caring.',
                'date' => '2024-01-15'
            ],
            [
                'name' => 'Michael Chen',
                'rating' => 5,
                'comment' => 'Easy to book appointments and great follow-up care.',
                'date' => '2024-01-10'
            ],
            [
                'name' => 'Emily Davis',
                'rating' => 4,
                'comment' => 'Very convenient platform for healthcare needs.',
                'date' => '2024-01-08'
            ]
        ];
    }

    /**
     * Get team members
     */
    private function getTeamMembers()
    {
        // This would typically come from a team table
        // For now, return sample team members
        return [
            [
                'name' => 'Dr. John Smith',
                'position' => 'Chief Medical Officer',
                'specialty' => 'Internal Medicine',
                'image' => 'team/john-smith.jpg',
                'bio' => 'Experienced physician with over 15 years in healthcare management.'
            ],
            [
                'name' => 'Dr. Sarah Wilson',
                'position' => 'Medical Director',
                'specialty' => 'Cardiology',
                'image' => 'team/sarah-wilson.jpg',
                'bio' => 'Board-certified cardiologist specializing in preventive care.'
            ],
            [
                'name' => 'Dr. Michael Brown',
                'position' => 'Technical Director',
                'specialty' => 'Healthcare Technology',
                'image' => 'team/michael-brown.jpg',
                'bio' => 'Expert in healthcare technology and digital health solutions.'
            ]
        ];
    }

    /**
     * Get services
     */
    private function getServices()
    {
        return [
            [
                'title' => 'Online Appointment Booking',
                'description' => 'Book appointments with your preferred doctors at your convenience.',
                'icon' => 'calendar',
                'features' => [
                    '24/7 availability',
                    'Instant confirmation',
                    'Reminder notifications',
                    'Easy rescheduling'
                ]
            ],
            [
                'title' => 'Video Consultations',
                'description' => 'Connect with doctors remotely for consultations and follow-ups.',
                'icon' => 'video-camera',
                'features' => [
                    'Secure video calls',
                    'Screen sharing',
                    'Prescription delivery',
                    'Medical records access'
                ]
            ],
            [
                'title' => 'Health Records Management',
                'description' => 'Keep your medical history organized and accessible.',
                'icon' => 'document',
                'features' => [
                    'Digital health records',
                    'Test results storage',
                    'Medication tracking',
                    'Family health history'
                ]
            ],
            [
                'title' => 'Payment & Insurance',
                'description' => 'Secure payment processing and insurance integration.',
                'icon' => 'credit-card',
                'features' => [
                    'Multiple payment methods',
                    'Insurance claims',
                    'Payment history',
                    'Refund processing'
                ]
            ]
        ];
    }

    /**
     * Get FAQs
     */
    private function getFAQs()
    {
        return [
            [
                'question' => 'How do I book an appointment?',
                'answer' => 'You can book an appointment by creating an account, searching for a doctor, and selecting an available time slot. The booking process is simple and takes just a few minutes.'
            ],
            [
                'question' => 'Can I cancel or reschedule my appointment?',
                'answer' => 'Yes, you can cancel or reschedule your appointment up to 24 hours before the scheduled time. Simply go to your appointments section and make the necessary changes.'
            ],
            [
                'question' => 'How do video consultations work?',
                'answer' => 'Video consultations are conducted through our secure platform. You\'ll receive a link before your appointment. Make sure you have a stable internet connection and a device with camera and microphone.'
            ],
            [
                'question' => 'Is my medical information secure?',
                'answer' => 'Yes, we take your privacy and security seriously. All medical information is encrypted and stored securely in compliance with healthcare data protection regulations.'
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept major credit cards, debit cards, and digital wallets. We also work with most major insurance providers for covered services.'
            ],
            [
                'question' => 'How do I access my medical records?',
                'answer' => 'Your medical records are available in your patient dashboard. You can view, download, and share them with other healthcare providers as needed.'
            ]
        ];
    }

    /**
     * Display sitemap
     */
    public function sitemap()
    {
        $doctors = DB::table('doctors')
            ->where('is_active', true)
            ->select('id', 'name', 'updated_at')
            ->get();

        $specialties = DB::table('specialties')
            ->select('id', 'name_en', 'updated_at')
            ->get();

        return response()->view('sitemap', compact('doctors', 'specialties'))
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Display robots.txt
     */
    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /api/\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($content)->header('Content-Type', 'text/plain');
    }

    /**
     * Handle 404 errors
     */
    public function notFound()
    {
        return view('errors.404');
    }

    /**
     * Handle 500 errors
     */
    public function serverError()
    {
        return view('errors.500');
    }

    /**
     * Display maintenance page
     */
    public function maintenance()
    {
        return view('maintenance');
    }

    /**
     * Check system status
     */
    public function status()
    {
        $status = [
            'database' => $this->checkDatabaseConnection(),
            'cache' => $this->checkCacheConnection(),
            'storage' => $this->checkStorageConnection(),
            'uptime' => $this->getSystemUptime(),
        ];

        return response()->json($status);
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'connected', 'message' => 'Database is connected'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
    }

    /**
     * Check cache connection
     */
    private function checkCacheConnection()
    {
        try {
            \Cache::store()->has('test');
            return ['status' => 'connected', 'message' => 'Cache is working'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache connection failed'];
        }
    }

    /**
     * Check storage connection
     */
    private function checkStorageConnection()
    {
        try {
            \Storage::disk('public')->exists('test');
            return ['status' => 'connected', 'message' => 'Storage is accessible'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Storage connection failed'];
        }
    }

    /**
     * Get system uptime
     */
    private function getSystemUptime()
    {
        // This would typically get actual system uptime
        // For now, return a placeholder
        return [
            'status' => 'running',
            'uptime' => '24 hours',
            'last_restart' => now()->subDay()->toISOString()
        ];
    }
}
