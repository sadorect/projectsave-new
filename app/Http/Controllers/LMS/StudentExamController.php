<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Course;
use App\Models\ExamAttempt;
use App\Models\Certificate;
use App\Models\User;
use App\Notifications\ExamResultsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StudentExamController extends Controller
{
    /**
     * Display available exams for the authenticated student
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get completed courses with their exams
        $completedCoursesWithExams = Course::whereHas('users', function($query) use ($user) {
        $query->where('user_id', $user->id);
    })
    ->with(['exams' => function($query) {
        $query->where('is_active', true)
              ->withCount('questions');
    }])
    ->get()
    ->filter(function($course) use ($user) {
        return $course->isCompletedByStudent($user) &&
               $course->exams->where('questions_count', '>=', 5)->count() > 0;
    });

        // Get exam attempts for this user
        $examAttempts = ExamAttempt::where('user_id', $user->id)
            ->with('exam')
            ->get()
            ->groupBy('exam_id');

        return view('lms.exams.index', compact('completedCoursesWithExams', 'examAttempts'));
    }

    /**
     * Show exam details and start page
     */
    public function show(Exam $exam)
    {
        $user = Auth::user();
        
        // Check if user can access this exam
        if (!$exam->isAvailableForStudent($user)) {
            return redirect()->route('lms.exams.index')
                ->with('error', 'You must complete the course before taking this exam.');
        }

        // Get user's attempts for this exam
        $attempts = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate remaining attempts
        $remainingAttempts = $exam->max_attempts - $attempts->count();
        
        // Get the last attempt (most recent)
        $lastAttempt = $attempts->first();
        
        // Determine if user can retake the exam
        $canRetake = false;
        
        if ($remainingAttempts > 0) {
            // If no attempts yet, allow first attempt
            if ($attempts->count() === 0) {
                $canRetake = true;
            }
            // If retakes are allowed and last attempt failed or incomplete
            elseif ($exam->allow_retakes && $lastAttempt) {
                if (!$lastAttempt->completed_at || $lastAttempt->score < $exam->passing_score) {
                    $canRetake = true;
                }
            }
            // If retakes not allowed but user hasn't passed yet
            elseif (!$exam->allow_retakes && $lastAttempt && $lastAttempt->score < $exam->passing_score) {
                $canRetake = true;
            }
        }

        return view('lms.exams.show', compact('exam', 'attempts', 'remainingAttempts', 'lastAttempt', 'canRetake'));
    }

    /**
     * Start a new exam attempt
     */
    public function start(Request $request, Exam $exam)
    {
        $user = Auth::user();
        
        // Validate access
        if (!$exam->isAvailableForStudent($user)) {
            return redirect()->route('lms.exams.index')
                ->with('error', 'You cannot access this exam.');
        }

        // Check remaining attempts
        $attemptCount = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->count();

        if ($attemptCount >= $exam->max_attempts) {
            return redirect()->route('lms.exams.show', $exam)
                ->with('error', 'You have reached the maximum number of attempts for this exam.');
        }

        // Create new attempt
        $attempt = ExamAttempt::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now(),
            'answers' => []
        ]);

        return redirect()->route('lms.exams.take', [$exam, $attempt]);
    }

    /**
     * Take the exam interface
     */
    public function take(Exam $exam, ExamAttempt $attempt)
    {
        $user = Auth::user();
        
        // Verify this attempt belongs to the current user
        if ($attempt->user_id !== $user->id || $attempt->exam_id !== $exam->id) {
            abort(403);
        }

        // Check if attempt is already completed
        if ($attempt->completed_at) {
            return redirect()->route('lms.exams.results', [$exam, $attempt]);
        }

        // Check if time has expired
        $timeLimit = $exam->duration_minutes * 60; // Convert to seconds
        $elapsedTime = now()->diffInSeconds($attempt->started_at);
        
        if ($elapsedTime >= $timeLimit) {
            // Auto-submit the exam
            return $this->submit($exam, $attempt, new Request());
        }

        $questions = $exam->questions()->inRandomOrder()->get();
        $remainingTime = max(0, $timeLimit - $elapsedTime);

        return view('lms.exams.take', compact('exam', 'attempt', 'questions', 'remainingTime'));
    }

    /**
     * Save answer (AJAX endpoint for auto-save)
     */
    public function saveAnswer(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        $user = Auth::user();
        
        if ($attempt->user_id !== $user->id || $attempt->completed_at) {
            return response()->json(['error' => 'Invalid attempt'], 403);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string'
        ]);

        $answers = $attempt->answers ?? [];
        $answers[$request->question_id] = [
            'answer' => $request->answer,
            'answered_at' => now()->toISOString(),
            'time_spent' => $request->time_spent ?? 0
        ];

        $attempt->update(['answers' => $answers]);

        return response()->json(['success' => true]);
    }

    /**
     * Submit exam attempt
     */
    public function submit(Exam $exam, ExamAttempt $attempt, Request $request = null)
    {
        $user = Auth::user();
        
        if ($attempt->user_id !== $user->id || $attempt->completed_at) {
            return redirect()->route('lms.exams.results', [$exam, $attempt]);
        }

        // If submitting via form, save final answers
        if ($request && $request->has('answers')) {
            $formAnswers = $request->input('answers', []);
            $existingAnswers = $attempt->answers ?? [];
            
            foreach ($formAnswers as $questionId => $answer) {
                $existingAnswers[$questionId] = [
                    'answer' => $answer,
                    'answered_at' => now()->toISOString(),
                    'time_spent' => $existingAnswers[$questionId]['time_spent'] ?? 0
                ];
            }
            
            $attempt->update(['answers' => $existingAnswers]);
        }

        // Calculate score
        $score = $this->calculateScore($exam, $attempt);
        
        $attempt->update([
            'completed_at' => now(),
            'score' => $score
        ]);

        // Send email notification about results
        $this->sendResultsEmail($user, $exam, $attempt);

        // Check if this completion qualifies for Diploma in Ministry certificate
        $this->checkAndGenerateDiplomaCertificate($user, $exam, $attempt);

        return redirect()->route('lms.exams.results', [$exam, $attempt])
            ->with('success', 'Exam submitted successfully!');
    }

    /**
     * Show exam results
     */
    public function results(Exam $exam, ExamAttempt $attempt)
    {
        $user = Auth::user();
        
        if ($attempt->user_id !== $user->id) {
            abort(403);
        }

        if (!$attempt->completed_at) {
            return redirect()->route('lms.exams.take', [$exam, $attempt]);
        }

        $questions = $exam->questions;
        $answers = $attempt->answers ?? [];
        $passed = $attempt->score >= $exam->passing_score;
        
        // Calculate detailed analytics
        $analytics = $this->calculateDetailedAnalytics($exam, $attempt, $questions, $answers);

        return view('lms.exams.results', compact('exam', 'attempt', 'questions', 'answers', 'passed', 'analytics'));
    }

    /**
     * Calculate exam score
     */
    private function calculateScore(Exam $exam, ExamAttempt $attempt)
    {
        $questions = $exam->questions;
        $answers = $attempt->answers ?? [];
        $totalPoints = $questions->sum('points');
        $earnedPoints = 0;

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id]['answer'] ?? null;
            if ($userAnswer === $question->correct_answer) {
                $earnedPoints += $question->points;
            }
        }

        return $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
    }

    /**
     * Calculate detailed analytics
     */
    private function calculateDetailedAnalytics(Exam $exam, ExamAttempt $attempt, $questions, $answers)
    {
        $totalQuestions = $questions->count();
        $answeredQuestions = count($answers);
        $correctAnswers = 0;
        $totalTimeSpent = 0;
        $questionBreakdown = [];

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $isCorrect = $userAnswer && $userAnswer['answer'] === $question->correct_answer;
            $timeSpent = $userAnswer['time_spent'] ?? 0;
            
            if ($isCorrect) {
                $correctAnswers++;
            }
            
            $totalTimeSpent += $timeSpent;
            
            $questionBreakdown[] = [
                'question' => $question,
                'user_answer' => $userAnswer['answer'] ?? null,
                'correct_answer' => $question->correct_answer,
                'is_correct' => $isCorrect,
                'time_spent' => $timeSpent,
                'points_earned' => $isCorrect ? $question->points : 0
            ];
        }

        return [
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'correct_answers' => $correctAnswers,
            'accuracy_percentage' => $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0,
            'total_time_spent' => $totalTimeSpent,
            'average_time_per_question' => $totalQuestions > 0 ? round($totalTimeSpent / $totalQuestions, 2) : 0,
            'exam_duration' => $attempt->started_at && $attempt->completed_at ? 
                $attempt->started_at->diffInSeconds($attempt->completed_at) : 0,
            'question_breakdown' => $questionBreakdown
        ];
    }

    /**
     * Send email notification about exam results
     */
    private function sendResultsEmail($user, $exam, $attempt)
    {
        try {
            $user->notify(new ExamResultsNotification($exam, $attempt));
        } catch (\Exception $e) {
            // Log error but don't fail the exam submission
            Log::error('Failed to send exam results email', [
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'attempt_id' => $attempt->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if user qualifies for Diploma in Ministry certificate
     * and generate it (pending admin approval)
     */
    private function checkAndGenerateDiplomaCertificate($user, $exam, $attempt)
    {
        // Only proceed if exam was passed
        if ($attempt->score < $exam->passing_score) {
            return;
        }

        // Check if user already has a diploma certificate (pending or approved)
        $existingCertificate = Certificate::where('user_id', $user->id)
            ->whereNull('course_id') // Diploma certificates don't have course_id
            ->first();

        if ($existingCertificate) {
            return; // Already has diploma certificate
        }

        // Define the ASOM course titles that make up the Diploma in Ministry program
        $asomCourseTitles = [
            'Bible Introduction',
            'Hermeneutics',
            'Ministry Vitals',
            'Spiritual Gifts & Ministry',
            'Biblical Counseling',
            'Homiletics'
        ];

        // Check if user has completed all ASOM courses with passing exam scores
        $completedAsomCourses = Course::whereIn('title', $asomCourseTitles)
            ->whereHas('users', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['exams' => function($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->filter(function($course) use ($user) {
                // Check if course is completed by student
                if (!$course->isCompletedByStudent($user)) {
                    return false;
                }

                // Require that the course has exams and that ALL exams have been passed by the user
                if ($course->exams->count() === 0) {
                    return false; // No exams means the course can't qualify for diploma's exam requirement
                }

                $allExamsPassed = $course->exams->every(function($courseExam) use ($user) {
                    return ExamAttempt::where('user_id', $user->id)
                        ->where('exam_id', $courseExam->id)
                        ->where('score', '>=', $courseExam->passing_score)
                        ->whereNotNull('completed_at')
                        ->exists();
                });

                return $allExamsPassed;
            });

        // If user has completed all required ASOM courses with passing exams
        if ($completedAsomCourses->count() === count($asomCourseTitles)) {
            $this->generateDiplomaCertificate($user, $completedAsomCourses);
        }
    }

    /**
     * Generate Diploma in Ministry certificate (pending admin approval)
     */
    private function generateDiplomaCertificate($user, $completedCourses)
    {
        // Calculate overall grade based on all exam attempts
        $totalScore = 0;
        $examCount = 0;

        foreach ($completedCourses as $course) {
            foreach ($course->exams as $exam) {
                $bestAttempt = ExamAttempt::where('user_id', $user->id)
                    ->where('exam_id', $exam->id)
                    ->where('score', '>=', $exam->passing_score)
                    ->whereNotNull('completed_at')
                    ->orderBy('score', 'desc')
                    ->first();

                if ($bestAttempt) {
                    $totalScore += $bestAttempt->score;
                    $examCount++;
                }
            }
        }

        $finalGrade = $examCount > 0 ? round($totalScore / $examCount, 2) : 0;

        // Create certificate pending admin approval
        Certificate::create([
            'user_id' => $user->id,
            'course_id' => null, // Diploma certificates don't belong to a single course
            'final_grade' => $finalGrade,
            'completed_at' => now(),
            'issued_at' => null, // Will be set when admin approves
            'is_approved' => false, // Requires admin approval
            'notes' => 'Certificate of Diploma in Ministry - Completed all ASOM courses with passing grades'
        ]);

        Log::info('Diploma in Ministry certificate generated (pending approval)', [
            'user_id' => $user->id,
            'final_grade' => $finalGrade,
            'completed_courses' => $completedCourses->count()
        ]);
    }

    /**
     * Check if user is eligible for Diploma in Ministry certificate
     */
    public function checkDiplomaEligibility($userId = null)
    {
        $user = $userId ? User::find($userId) : Auth::user();
        
        if (!$user) {
            return ['eligible' => false, 'message' => 'User not found'];
        }

        // Define the ASOM course titles
        $asomCourseTitles = [
            'Bible Introduction',
            'Hermeneutics',
            'Ministry Vitals',
            'Spiritual Gifts & Ministry',
            'Biblical Counseling',
            'Homiletics'
        ];

        $completedCourses = Course::whereIn('title', $asomCourseTitles)
            ->whereHas('users', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['exams'])
            ->get()
            ->filter(function($course) use ($user) {
                return $course->isCompletedByStudent($user);
            });

        $passedExams = [];
        $totalRequired = count($asomCourseTitles);
        
        foreach ($completedCourses as $course) {
            foreach ($course->exams as $exam) {
                $passingAttempt = ExamAttempt::where('user_id', $user->id)
                    ->where('exam_id', $exam->id)
                    ->where('score', '>=', $exam->passing_score)
                    ->whereNotNull('completed_at')
                    ->first();
                
                if ($passingAttempt) {
                    $passedExams[] = $course->title;
                    break; // Only need one passing exam per course
                }
            }
        }

        $completedRequirements = count(array_unique($passedExams));
        
        return [
            'eligible' => $completedRequirements === $totalRequired,
            'completed' => $completedRequirements,
            'total_required' => $totalRequired,
            'completed_courses' => array_unique($passedExams),
            'remaining_courses' => array_diff($asomCourseTitles, array_unique($passedExams))
        ];
    }
}
