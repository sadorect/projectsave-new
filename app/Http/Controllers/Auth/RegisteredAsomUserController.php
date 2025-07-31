<?php

namespace App\Http\Controllers\Auth;

use Log;
use App\Models\User;
use App\Models\Course;
use App\Rules\Recaptcha;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;

class RegisteredAsomUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.asom-register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => ['required', new Recaptcha],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'asom_student', // Mark as ASOM student
        ]);

        $user->sendEmailVerificationNotification();
        event(new Registered($user));

        Auth::login($user);

       // Store intended redirect in session for after email verification
       session(['asom_redirect_after_verification' => true]);

       // Redirect to email verification notice with ASOM context
       return redirect()->route('verification.notice')->with('asom_student', true);
    }

    /**
     * Display the ASOM welcome page with WhatsApp groups.
     */
    public function welcome(): View
    {
        $user = Auth::user();
        
        // Get all ASOM-related courses
        $asomCourses = Course::with(['lessons', 'instructor'])
            ->where('status', 'published')
            ->get();

        // Get user's enrolled courses (filtered to ASOM courses only)
        $enrolledCourses = $user->courses()
            ->withPivot('status', 'enrolled_at', 'completed_at')
            ->get();

        // Calculate course statistics
        $stats = [
            'total_courses' => $asomCourses->count(),
            'enrolled_courses' => $enrolledCourses->count(),
            'completed_courses' => $enrolledCourses->where('pivot.status', 'completed')->count(),
            'in_progress_courses' => $enrolledCourses->where('pivot.status', 'active')->count(),
            'overall_progress' => $this->calculateOverallProgress($enrolledCourses)
        ];

        // Map all courses with enrollment and progress data
        $allCourses = $asomCourses->map(function ($course) use ($user, $enrolledCourses) {
            $enrolledCourse = $enrolledCourses->where('id', $course->id)->first();
            $isEnrolled = $enrolledCourse !== null;
            $progress = $isEnrolled ? $user->getCourseProgress($course) : 0;
            
            return [
                'id' => $course->id,
                'slug' => $course->slug,
                'name' => $course->title,
                'description' => $course->description,
                'icon' => $this->getCourseIcon($course->title),
                'progress' => round($progress),
                'lessons' => $course->lessons()->count(),
                'is_enrolled' => $isEnrolled,
                'enrollment_status' => $isEnrolled ? $enrolledCourse->pivot->status : null,
                'enrolled_at' => $isEnrolled ? $enrolledCourse->pivot->enrolled_at : null,
                'instructor' => $course->instructor ? $course->instructor->name : 'TBA',
                'featured_image' => $course->featured_image
            ];
        });

        // Separate enrolled and available courses
        $enrolledCoursesWithProgress = $allCourses->where('is_enrolled', true)->values();
        $availableCoursesWithProgress = $allCourses->where('is_enrolled', false)->values();

        // Calculate achievements
        $achievements = $this->calculateAchievements($user, $enrolledCourses);

        // Get exam data for completed courses
        $examData = $this->getExamData($user, $enrolledCoursesWithProgress);

        $whatsappGroups = [
            [
                'name' => 'Bible Introduction',
                'url' => 'https://chat.whatsapp.com/BVLhGpQ6PSKJQB1Mj76MfY',
                'icon' => 'fas fa-book-open',
                'description' => 'Introduction to Biblical studies and foundational concepts'
            ],
            [
                'name' => 'Hermeneutics',
                'url' => 'https://chat.whatsapp.com/ChWdu5pFXnZK78CjxOG4ec',
                'icon' => 'fas fa-search',
                'description' => 'Biblical interpretation principles and methods'
            ],
            [
                'name' => 'Ministry Vitals',
                'url' => 'https://chat.whatsapp.com/JgEKk0Ae4b73zDptc4jTow',
                'icon' => 'fas fa-heart',
                'description' => 'Essential principles for effective ministry'
            ],
            [
                'name' => 'Spiritual Gifts & Ministry',
                'url' => 'https://chat.whatsapp.com/FgWiscG4Xh7A9ueuuOzACG',
                'icon' => 'fas fa-gifts',
                'description' => 'Discovering and using your spiritual gifts'
            ],
            [
                'name' => 'Biblical Counseling',
                'url' => 'https://chat.whatsapp.com/HBthpjWrv9q9nCGN6qi18V',
                'icon' => 'fas fa-hands-helping',
                'description' => 'Biblical approaches to counseling and care'
            ],
            [
                'name' => 'Homiletics',
                'url' => 'https://chat.whatsapp.com/JHhtdqlSTSd5uF3oUAOBly',
                'icon' => 'fas fa-microphone',
                'description' => 'Art and science of preaching and sermon preparation'
            ],
            [
                'name' => 'ASOM Recharge',
                'url' => 'https://chat.whatsapp.com/CnQijuSNwLe50yNald4aob',
                'icon' => 'fas fa-battery-full',
                'description' => 'Spiritual refreshment and encouragement'
            ],
            [
                'name' => 'Info Desk',
                'url' => 'https://chat.whatsapp.com/CD1sL6mRamMKErBimzz52f',
                'icon' => 'fas fa-info-circle',
                'description' => 'General information and administrative support'
            ]
        ];

        return view('asom-welcome', compact('whatsappGroups', 'stats', 'allCourses', 'enrolledCoursesWithProgress', 'availableCoursesWithProgress', 'achievements', 'examData'));
    }

    private function calculateOverallProgress($enrolledCourses)
    {
        if ($enrolledCourses->isEmpty()) {
            return 0;
        }

        $totalProgress = $enrolledCourses->sum(function ($course) {
            return Auth::user()->getCourseProgress($course);
        });

        return round($totalProgress / $enrolledCourses->count(), 1);
    }

    private function getCourseIcon($courseTitle)
    {
        $icons = [
            'Bible Introduction' => 'fas fa-book-open',
            'Hermeneutics' => 'fas fa-search',
            'Ministry Vitals' => 'fas fa-heart',
            'Spiritual Gifts & Ministry' => 'fas fa-gifts',
            'Biblical Counseling' => 'fas fa-hands-helping',
            'Homiletics' => 'fas fa-microphone'
        ];

        return $icons[$courseTitle] ?? 'fas fa-book';
    }

    private function getExamData($user, $enrolledCoursesWithProgress)
    {
        // Get courses that are 100% complete
        $completedCourses = $enrolledCoursesWithProgress->where('progress', 100);
        
        if ($completedCourses->isEmpty()) {
            return [
                'available_exams' => 0,
                'completed_exams' => 0,
                'passed_exams' => 0,
                'pending_exams' => [],
                'recent_results' => []
            ];
        }
        
        // Get available exams for completed courses
        $availableExams = \App\Models\Exam::whereIn('course_id', $completedCourses->pluck('id'))
            ->where('is_active', true)
            ->with('course', 'questions')
            ->get()
            ->filter(function($exam) {
        return $exam->questions->count() >= 5;
    });
        
        // Get user's exam attempts
        $examAttempts = \App\Models\ExamAttempt::where('user_id', $user->id)
            ->whereIn('exam_id', $availableExams->pluck('id'))
            ->with('exam.course')
            ->orderBy('completed_at', 'desc')
            ->get();
        
        $completedAttempts = $examAttempts->whereNotNull('completed_at');
        $passedAttempts = $completedAttempts->filter(function($attempt) {
            return $attempt->score >= $attempt->exam->passing_score;
        });
        
        // Get pending exams (available but not taken or failed)
        $pendingExams = $availableExams->filter(function($exam) use ($passedAttempts) {
            return !$passedAttempts->contains('exam_id', $exam->id);
        })->take(3)->map(function($exam) {
            return [
                'id' => $exam->id,
                'title' => $exam->title,
                'course' => $exam->course->title,
                'duration' => $exam->duration_minutes,
                'questions' => $exam->questions()->count()
            ];
        });
        
        // Get recent exam results
        $recentResults = $completedAttempts->take(3)->map(function($attempt) {
            return [
                'exam_title' => $attempt->exam->title,
                'course_title' => $attempt->exam->course->title,
                'score' => $attempt->score,
                'passed' => $attempt->score >= $attempt->exam->passing_score,
                'completed_at' => $attempt->completed_at->format('M j, Y'),
                'exam_id' => $attempt->exam_id,
                'attempt_id' => $attempt->id
            ];
        });
        
        return [
            'available_exams' => $availableExams->count(),
            'completed_exams' => $completedAttempts->unique('exam_id')->count(),
            'passed_exams' => $passedAttempts->unique('exam_id')->count(),
            'pending_exams' => $pendingExams,
            'recent_results' => $recentResults
        ];
    }

    private function calculateAchievements($user, $enrolledCourses)
    {
        $completedCourses = $enrolledCourses->where('pivot.status', 'completed');
        $totalCompleted = $completedCourses->count();
        
        return [
            'first_course' => $totalCompleted >= 1,
            'bible_scholar' => $completedCourses->whereIn('title', ['Bible Introduction', 'Hermeneutics'])->count() >= 2,
            'community_builder' => $user->hasVerifiedEmail(), // Simplified for now
            'preacher' => $completedCourses->where('title', 'Homiletics')->count() >= 1,
            'counselor' => $completedCourses->where('title', 'Biblical Counseling')->count() >= 1,
            'graduate' => $totalCompleted >= 6
        ];
    }

    // Add this method to your existing RegisteredAsomUserController

/**
 * Convert existing user to ASOM student
 */
  public function convertToAsomStudent(Request $request): RedirectResponse
  {
      //$user = Auth::user();
      $user = User::find($request->user_id);

    // Debug: Check if user is authenticated
    if (!$user) {
      return redirect()->back()->with('error', 'You must be logged in to join ASOM.');
  }
  
  /* Debug: Log the user info
  Log::info('ASOM Join attempt', [
      'user_id' => $user->id,
      'current_user_type' => $user->user_type,
      'email' => $user->email
  ]);*/



      // Check if user is already an ASOM student
      if ($user->user_type === 'asom_student') {
          return redirect()->route('asom.welcome')->with('info', 'You are already enrolled in ASOM!');
      }
      
      // Update user type to ASOM student
    $user->user_type = 'asom_student';
    $user->save();
      
      // Optional: Send welcome email or notification
      // $user->notify(new AsomWelcomeNotification());
      
      return redirect()->route('asom.welcome')->with('success', 'Welcome to ASOM! You have been successfully enrolled.');
  }

}
