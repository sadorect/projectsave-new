<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Notifications\CertificateApprovedNotification;

class AdminCertificateController extends Controller
{
    /**
     * Display all certificates with filtering options
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'course', 'approver'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->where('is_approved', false)->whereNull('approved_at');
                    break;
                case 'approved':
                    $query->where('is_approved', true);
                    break;
                case 'diploma':
                    $query->whereNull('course_id'); // Diploma certificates don't have course_id
                    break;
            }
        }

        // Filter by user
        if ($request->has('user') && $request->user) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%')
                  ->orWhere('email', 'like', '%' . $request->user . '%');
            });
        }

        $certificates = $query->paginate(15);

        $stats = [
            'total' => Certificate::count(),
            'pending' => Certificate::where('is_approved', false)->whereNull('approved_at')->count(),
            'approved' => Certificate::where('is_approved', true)->count(),
            'diploma_certificates' => Certificate::whereNull('course_id')->count(),
        ];

        return view('admin.certificates.index', compact('certificates', 'stats'));
    }

    /**
     * Display pending certificates that need approval
     */
    public function pending()
    {
        $pendingCertificates = Certificate::with(['user', 'course'])
            ->where('is_approved', false)
            ->whereNull('approved_at')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.certificates.pending', compact('pendingCertificates'));
    }

    /**
     * Show certificate details
     */
    public function show(Certificate $certificate)
    {
        $certificate->load(['user', 'course', 'approver']);
        
        // Get user's course completion details for diploma certificates
        if (!$certificate->course_id) {
            $asomCourseTitles = [
                'Bible Introduction',
                'Hermeneutics',
                'Ministry Vitals',
                'Spiritual Gifts & Ministry',
                'Biblical Counseling',
                'Homiletics'
            ];

            $userCourseDetails = $this->getUserAsomCourseDetails($certificate->user, $asomCourseTitles);
            return view('admin.certificates.show', compact('certificate', 'userCourseDetails'));
        }

        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Approve a certificate
     */
    public function approve(Request $request, Certificate $certificate)
    {
        if ($certificate->is_approved) {
            return back()->with('error', 'Certificate is already approved.');
        }

        $admin = Auth::user();
        
        $certificate->update([
            'is_approved' => true,
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'issued_at' => now(),
            'notes' => $request->input('notes', $certificate->notes)
        ]);

        Log::info('Certificate approved by admin', [
            'certificate_id' => $certificate->id,
            'user_id' => $certificate->user_id,
            'admin_id' => $admin->id,
            'certificate_type' => $certificate->course_id ? 'course' : 'diploma'
        ]);

        // Notify student about certificate approval
        try {
            $certificate->user->notify(new CertificateApprovedNotification($certificate));
        } catch (\Throwable $e) {
            Log::error('Failed to send CertificateApprovedNotification', [
                'certificate_id' => $certificate->id,
                'user_id' => $certificate->user_id,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', 'Certificate approved successfully!');
    }

    /**
     * Reject a certificate (soft delete or mark as rejected)
     */
    public function reject(Request $request, Certificate $certificate)
    {
        if ($certificate->is_approved) {
            return back()->with('error', 'Cannot reject an already approved certificate.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $admin = Auth::user();
        
        $certificate->update([
            'notes' => 'REJECTED: ' . $request->rejection_reason . ' (Rejected by: ' . $admin->name . ')',
        ]);

        // Soft delete the certificate
        $certificate->delete();

        Log::info('Certificate rejected by admin', [
            'certificate_id' => $certificate->id,
            'user_id' => $certificate->user_id,
            'admin_id' => $admin->id,
            'reason' => $request->rejection_reason
        ]);

        // TODO: Send notification to student about certificate rejection
        // $certificate->user->notify(new CertificateRejectedNotification($certificate, $request->rejection_reason));

        return back()->with('success', 'Certificate rejected successfully.');
    }

    /**
     * Bulk approve multiple certificates
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'certificate_ids' => 'required|array',
            'certificate_ids.*' => 'exists:certificates,id'
        ]);

        $admin = Auth::user();
        $approvedCount = 0;

        foreach ($request->certificate_ids as $certificateId) {
            $certificate = Certificate::find($certificateId);
            
            if ($certificate && !$certificate->is_approved) {
                $certificate->update([
                    'is_approved' => true,
                    'approved_by' => $admin->id,
                    'approved_at' => now(),
                    'issued_at' => now(),
                ]);
                $approvedCount++;
            }
        }

        Log::info('Bulk certificate approval', [
            'admin_id' => $admin->id,
            'approved_count' => $approvedCount,
            'certificate_ids' => $request->certificate_ids
        ]);

        return back()->with('success', "Successfully approved {$approvedCount} certificates.");
    }

    /**
     * Get detailed course completion info for a user's ASOM diploma
     */
    private function getUserAsomCourseDetails($user, $asomCourseTitles)
    {
        $courseDetails = [];
        
        foreach ($asomCourseTitles as $courseTitle) {
            $course = \App\Models\Course::where('title', $courseTitle)
                ->whereHas('users', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['exams'])
                ->first();

            if ($course) {
                $isCompleted = $course->isCompletedByStudent($user);
                $examResults = [];

                foreach ($course->exams as $exam) {
                    $attempts = \App\Models\ExamAttempt::where('user_id', $user->id)
                        ->where('exam_id', $exam->id)
                        ->whereNotNull('completed_at')
                        ->orderBy('score', 'desc')
                        ->get();

                    $bestScore = $attempts->max('score');
                    $passed = $bestScore >= $exam->passing_score;

                    $examResults[] = [
                        'exam_title' => $exam->title,
                        'best_score' => $bestScore,
                        'passing_score' => $exam->passing_score,
                        'passed' => $passed,
                        'attempts_count' => $attempts->count()
                    ];
                }

                $courseDetails[] = [
                    'course_title' => $courseTitle,
                    'completed' => $isCompleted,
                    'exam_results' => $examResults,
                    'enrolled_at' => $course->pivot->created_at ?? null
                ];
            } else {
                $courseDetails[] = [
                    'course_title' => $courseTitle,
                    'completed' => false,
                    'exam_results' => [],
                    'enrolled_at' => null
                ];
            }
        }

        return $courseDetails;
    }

    /**
     * Export certificates data
     */
    public function export(Request $request)
    {
        // TODO: Implement CSV/Excel export functionality
        return back()->with('info', 'Export functionality will be implemented soon.');
    }

    /**
     * Scan system for students who have passed ALL ASOM program exams but lack a diploma certificate.
     * Creates pending Diploma in Ministry certificates for admin approval.
     */
    public function scanMissing(Request $request)
    {
        $admin = Auth::user();
        $createdDiplomaCerts = 0;

        // 2) Diploma-level certificates: ASOM specific list
        $asomCourseTitles = [
            'Bible Introduction',
            'Hermeneutics',
            'Ministry Vitals',
            'Spiritual Gifts & Ministry',
            'Biblical Counseling',
            'Homiletics'
        ];

    $asomCourses = Course::whereIn('title', $asomCourseTitles)->with('exams')->get();

        // Find all users enrolled in any of the ASOM courses
        $userIds = DB::table('course_user')
            ->whereIn('course_id', $asomCourses->pluck('id'))
            ->pluck('user_id')
            ->unique();

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            // Skip if diploma certificate already exists
            $existingDiploma = Certificate::where('user_id', $user->id)
                ->whereNull('course_id')
                ->first();
            if ($existingDiploma) continue;

            // Ensure PASSED exams across all ASOM courses (active with enough questions)
            $totalScore = 0; $examCount = 0; $allPassed = true; $completedDates = [];
            foreach ($asomCourses as $course) {
                $courseExams = $course->exams()
                    ->where('is_active', true)
                    ->withCount('questions')
                    ->get()
                    ->filter(fn($exam) => $exam->questions_count >= 5);

                if ($courseExams->isEmpty()) { $allPassed = false; break; }

                foreach ($courseExams as $exam) {
                    $bestAttempt = ExamAttempt::where('user_id', $user->id)
                        ->where('exam_id', $exam->id)
                        ->where('score', '>=', $exam->passing_score)
                        ->whereNotNull('completed_at')
                        ->orderBy('score', 'desc')
                        ->first();
                    if ($bestAttempt) {
                        $totalScore += $bestAttempt->score; $examCount++;
                        $completedDates[] = $bestAttempt->completed_at;
                    } else {
                        $allPassed = false; break 2;
                    }
                }
            }

            if (!$allPassed || $examCount === 0) continue;

            $finalGrade = round($totalScore / $examCount, 2);
            $completedAt = collect($completedDates)->filter()->max() ?? now();

            Certificate::create([
                'user_id' => $user->id,
                'course_id' => null,
                'final_grade' => $finalGrade,
                'completed_at' => $completedAt,
                'issued_at' => null,
                'is_approved' => false,
                'notes' => 'Auto-generated Diploma in Ministry certificate by admin scan (pending approval)'
            ]);
            $createdDiplomaCerts++;
        }

        Log::info('Admin scan for missing certificates completed', [
            'admin_id' => $admin->id,
            'created_diploma_certs' => $createdDiplomaCerts,
        ]);

        return redirect()->route('admin.certificates.index')
            ->with('success', "Scan complete. Created {$createdDiplomaCerts} diploma certificate(s). New certificates are pending approval.");
    }

    /**
     * Generate a sample certificate for testing (Admin only)
     */
    public function generateSample(Request $request)
    {
        $admin = Auth::user();
        
        // Create a sample Diploma in Ministry certificate for testing
        $sampleCertificate = Certificate::create([
            'user_id' => $admin->id, // Use admin as the sample student
            'course_id' => null, // Diploma certificates don't belong to a single course (null is now allowed)
            'course_title' => 'Diploma in Ministry', // Sample title
            'final_grade' => 95.50, // Sample grade
            'completed_at' => now()->subDays(7), // Completed 7 days ago
            'issued_at' => null, // Will be set when approved by admin
            'is_approved' => false, // Start as pending to test approval workflow
            'notes' => 'SAMPLE DIPLOMA CERTIFICATE - Generated for testing purposes by Admin: ' . $admin->name
        ]);

        Log::info('Sample Diploma certificate generated for testing', [
            'certificate_id' => $sampleCertificate->certificate_id,
            'admin_id' => $admin->id,
            'sample' => true
        ]);

        return redirect()->route('admin.certificates.show', $sampleCertificate)
            ->with('success', 'Sample Diploma in Ministry certificate generated successfully! Certificate ID: ' . $sampleCertificate->certificate_id);
    }

    /**
     * Generate sample course certificate for testing
     */
    public function generateSampleCourse(Request $request)
    {
        $admin = Auth::user();
        
        // Find a random course for sample certificate
        $sampleCourse = \App\Models\Course::whereIn('title', [
            'Bible Introduction',
            'Hermeneutics',
            'Ministry Vitals',
            'Spiritual Gifts & Ministry',
            'Biblical Counseling',
            'Homiletics'
        ])->first();

        if (!$sampleCourse) {
            return back()->with('error', 'No ASOM courses found to generate sample certificate.');
        }

        $sampleCertificate = Certificate::create([
            'user_id' => $admin->id,
            'course_title' => $sampleCourse->title,
            'course_id' => $sampleCourse->id,
            'final_grade' => rand(70, 100), // Random passing grade
            'completed_at' => now()->subDays(rand(1, 30)),
            'issued_at' => null,
            'is_approved' => false,
            'notes' => 'SAMPLE COURSE CERTIFICATE - Generated for testing purposes by Admin: ' . $admin->name
        ]);

        Log::info('Sample course certificate generated for testing', [
            'certificate_id' => $sampleCertificate->certificate_id,
            'course_id' => $sampleCourse->id,
            'admin_id' => $admin->id,
            'sample' => true
        ]);

        return redirect()->route('admin.certificates.show', $sampleCertificate)
            ->with('success', 'Sample course certificate generated successfully! Course: ' . $sampleCourse->title);
    }

    /**
     * Clean up sample certificates (Admin utility)
     */
    public function cleanupSamples()
    {
        $admin = Auth::user();
        
        $deletedCount = Certificate::where('notes', 'like', '%SAMPLE CERTIFICATE%')
            ->orWhere('notes', 'like', '%SAMPLE COURSE CERTIFICATE%')
            ->delete();

        Log::info('Sample certificates cleaned up', [
            'admin_id' => $admin->id,
            'deleted_count' => $deletedCount
        ]);

        return back()->with('success', "Cleaned up {$deletedCount} sample certificates.");
    }

    /**
     * Get certificate verification details for public verification
     */
    public function verify($certificateId)
    {
        $certificate = Certificate::where('certificate_id', $certificateId)
            ->where('is_approved', true)
            ->with(['user', 'course'])
            ->first();

        if (!$certificate) {
            return view('certificates.verify', ['certificate' => null]);
        }

        return view('certificates.verify', compact('certificate'));
    }
}
