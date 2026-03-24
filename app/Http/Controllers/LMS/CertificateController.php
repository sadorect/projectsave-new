<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;
use App\Support\Lms\DiplomaProgramService;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function __construct(private DiplomaProgramService $diplomaProgram)
    {
    }

    /**
     * Display student's certificates
     */
    public function index()
    {
        $certificates = Certificate::where('user_id', Auth::id())
            ->with(['course', 'approver'])
            ->orderByRaw('course_id is not null')
            ->orderBy('issued_at', 'desc')
            ->get();

        $certificateStats = [
            'total' => $certificates->count(),
            'approved' => $certificates->where('is_approved', true)->count(),
            'pending' => $certificates->where('is_approved', false)->count(),
            'program' => $certificates->whereNull('course_id')->count(),
        ];

        return view('lms.certificates.index', compact('certificates', 'certificateStats'));
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
        $isPdf = false; // Flag to indicate browser view

        return view('lms.certificates.certificate', compact(
            'student', 'course', 'completionDate', 'certificateId', 'finalGrade', 'certificate', 'isPdf'
        ));
    }

    /**
     * Generate certificate for course completion
     */
    public function generate(Course $course)
    {
        $user = Auth::user();
        $eligibility = $this->diplomaProgram->eligibility($user);

        if ($eligibility['certificate']) {
            return redirect()->route('lms.certificates.show', $eligibility['certificate'])
                ->with('info', 'Your ASOM program certificate is already on record.');
        }

        if (! $eligibility['eligible']) {
            return redirect()->route('lms.courses.show', $course->slug)
                ->with('info', 'ASOM issues one program certificate after all diploma courses and qualifying exams are completed.');
        }

        $certificate = $this->diplomaProgram->ensurePendingCertificate(
            $user,
            'Program certificate requested from the learner workspace after all ASOM diploma requirements were met.'
        );

        return redirect()->route('lms.certificates.show', $certificate)
            ->with('success', 'Your Diploma in Ministry certificate is now pending approval.');
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
        $isPdf = true; // Flag to indicate PDF generation

        $pdf = Pdf::loadView('lms.certificates.certificate', compact(
            'student', 'course', 'completionDate', 'certificateId', 'finalGrade', 'certificate', 'isPdf'
        ));

        // Configure PDF options for better color rendering
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'sans-serif',
            'dpi' => 150,
        ]);

        $courseSlug = $course ? $course->slug : 'diploma-in-ministry';
        $fileName = "Certificate_{$courseSlug}_{$student->name}_{$certificateId}.pdf";
        
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
