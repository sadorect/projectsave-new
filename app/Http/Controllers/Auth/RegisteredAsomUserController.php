<?php

namespace App\Http\Controllers\Auth;

use Log;
use App\Models\User;
use App\Models\Course;
use App\Rules\MathCaptchaRule;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use App\Support\Lms\StudentWorkspaceBuilder;

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
            'math_captcha' => ['required', new MathCaptchaRule],
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
    public function welcome(StudentWorkspaceBuilder $workspaceBuilder): View
    {
        return view('lms.dashboard.index', $workspaceBuilder->build(Auth::user()));
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
