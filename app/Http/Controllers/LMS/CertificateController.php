<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Display student's certificates
     */
    public function index()
    {
        $certificates = Certificate::where('user_id', Auth::id())
            ->with(['course', 'approver'])
            ->orderBy('issued_at', 'desc')
            ->get();

        return view('lms.certificates.index', compact('certificates'));
    }

    /**
     * Show a specific certificate
     */
    public function show(Certificate $certificate)
    {
        // Ensure user can only view their own certificates
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to certificate');
        }

        $student = $certificate->user;
        $course = $certificate->course;
        $completionDate = $certificate->completed_at;
        $certificateId = $certificate->certificate_id;
        $finalGrade = $certificate->final_grade;

        return view('lms.certificates.certificate', compact(
            'student', 'course', 'completionDate', 'certificateId', 'finalGrade', 'certificate'
        ));
    }

    /**
     * Generate certificate for course completion
     */
    public function generate(Course $course)
    {
        $user = Auth::user();
        
        // Check if user has completed the course
        $progress = $user->getCourseProgress($course);
        if ($progress < 100) {
            return redirect()->back()->with('error', 'You must complete the course before generating a certificate.');
        }

        // Check if certificate already exists
        $existingCertificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingCertificate) {
            return redirect()->route('lms.certificates.show', $existingCertificate)
                ->with('info', 'Certificate already exists for this course.');
        }

        // Get final grade from exams
        $finalGrade = null;
        $examAttempts = ExamAttempt::where('user_id', $user->id)
            ->whereHas('exam', function($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->whereNotNull('completed_at')
            ->get();

        if ($examAttempts->isNotEmpty()) {
            $finalGrade = $examAttempts->avg('score');
        }

        // Create certificate
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'final_grade' => $finalGrade,
            'issued_at' => now(),
            'completed_at' => now(), // You might want to get actual completion date
            'is_approved' => false, // Requires admin approval
        ]);

        return redirect()->route('lms.certificates.show', $certificate)
            ->with('success', 'Certificate generated successfully! It will be available for download once approved by an administrator.');
    }

    /**
     * Download certificate as PDF
     */
    public function download(Certificate $certificate)
    {
        // Ensure user can only download their own certificates
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to certificate');
        }

        // Only allow download if approved
        if (!$certificate->is_approved) {
            return redirect()->back()->with('error', 'Certificate must be approved before download.');
        }

        $student = $certificate->user;
        $course = $certificate->course;
        $completionDate = $certificate->completed_at;
        $certificateId = $certificate->certificate_id;
        $finalGrade = $certificate->final_grade;

        $pdf = Pdf::loadView('lms.certificates.certificate', compact(
            'student', 'course', 'completionDate', 'certificateId', 'finalGrade', 'certificate'
        ));

        $fileName = "Certificate_{$course->slug}_{$student->name}_{$certificateId}.pdf";
        
        return $pdf->download($fileName);
    }

    /**
     * Verify certificate by ID (public endpoint)
     */
    public function verify($certificateId)
    {
        $certificate = Certificate::where('certificate_id', $certificateId)
            ->with(['user', 'course', 'approver'])
            ->first();

        if (!$certificate) {
            return view('lms.certificates.verify', [
                'found' => false,
                'certificateId' => $certificateId
            ]);
        }

        return view('lms.certificates.verify', [
            'found' => true,
            'certificate' => $certificate,
            'certificateId' => $certificateId
        ]);
    }
}
