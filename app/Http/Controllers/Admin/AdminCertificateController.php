<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Support\Lms\DiplomaProgramService;
use Illuminate\Http\Request;
use App\Services\HtmlSanitizer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\CertificateApprovedNotification;

class AdminCertificateController extends Controller
{
    public function __construct(private DiplomaProgramService $diplomaProgram)
    {
        $this->middleware('can:viewAny,' . Certificate::class)->only(['index', 'pending', 'export']);
        $this->middleware('can:view,certificate')->only(['show', 'preview']);
        $this->middleware('can:approve,certificate')->only('approve');
        $this->middleware('can:reject,certificate')->only('reject');
        $this->middleware('can:regenerate,certificate')->only('regenerate');
        $this->middleware('can:delete,certificate')->only('destroy');
        $this->middleware('can:manage,' . Certificate::class)->only([
            'bulkApprove',
            'scanMissing',
            'generateSample',
            'generateSampleCourse',
            'cleanupSamples',
        ]);
    }

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
            $userCourseDetails = $this->diplomaProgram->userCourseDetails($certificate->user);
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
            'notes' => $request->filled('notes')
                ? $this->sanitizePlainText($request->input('notes'))
                : $certificate->notes
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
        $rejectionReason = $this->sanitizePlainText($request->rejection_reason);
        
        $certificate->update([
            'notes' => 'REJECTED: ' . $rejectionReason . ' (Rejected by: ' . $admin->name . ')',
        ]);

        // Soft delete the certificate
        $certificate->delete();

        Log::info('Certificate rejected by admin', [
            'certificate_id' => $certificate->id,
            'user_id' => $certificate->user_id,
            'admin_id' => $admin->id,
            'reason' => $rejectionReason
        ]);

        // TODO: Send notification to student about certificate rejection
        // $certificate->user->notify(new CertificateRejectedNotification($certificate, $request->rejection_reason));

        return back()->with('success', 'Certificate rejected successfully.');
    }

    /**
     * Permanently delete a certificate (including samples)
     */
    public function destroy(Request $request, Certificate $certificate)
    {
        $admin = Auth::user();

        // Deleting an approved certificate will invalidate its public verification URL
        $wasApproved = (bool) $certificate->is_approved;
        $certId = $certificate->certificate_id;

        $certificate->delete();

        Log::warning('Certificate deleted by admin', [
            'admin_id' => $admin->id,
            'certificate_id' => $certId,
            'was_approved' => $wasApproved,
        ]);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }

    /**
     * Regenerate a certificate: soft-delete current and create a fresh pending one
     */
    public function regenerate(Request $request, Certificate $certificate)
    {
        $admin = Auth::user();

        // Soft delete existing
        $certificate->delete();

        // Create replacement
        $new = Certificate::create([
            'user_id' => $certificate->user_id,
            'course_id' => $certificate->course_id, // null for diploma
            'final_grade' => $certificate->final_grade,
            'completed_at' => $certificate->completed_at,
            'issued_at' => null,
            'is_approved' => false,
            'notes' => trim(($certificate->notes ? $certificate->notes.' | ' : '') . 'Regenerated on '.now()->toDateTimeString().' by '.$admin->name),
        ]);

        Log::info('Certificate regenerated by admin', [
            'admin_id' => $admin->id,
            'old_certificate_id' => $certificate->certificate_id,
            'new_certificate_id' => $new->certificate_id,
        ]);

        return redirect()->route('admin.certificates.show', $new)
            ->with('success', 'Certificate regenerated. New certificate is pending approval.');
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

        $programCourseTitles = $this->diplomaProgram->requiredCourseTitles();
        $users = User::query()
            ->whereHas('courses', fn ($query) => $query->whereIn('courses.title', $programCourseTitles))
            ->get();

        foreach ($users as $user) {
            $certificate = $this->diplomaProgram->ensurePendingCertificate(
                $user,
                'Auto-generated diploma certificate by admin scan (pending approval).'
            );

            if ($certificate && $certificate->wasRecentlyCreated) {
                $createdDiplomaCerts++;
            }
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

    /**
     * Admin preview of a certificate using current certificate settings.
     * Renders the same Blade used by students/PDF, so changes in settings are visible immediately.
     */
    public function preview(Certificate $certificate)
    {
        $student = $certificate->user;
        $course = $certificate->course;
        $completionDate = $certificate->completed_at;
        $certificateId = $certificate->certificate_id;
        $finalGrade = $certificate->final_grade;
        $isPdf = false; // browser preview

        return view('lms.certificates.certificate', compact(
            'student', 'course', 'completionDate', 'certificateId', 'finalGrade', 'certificate', 'isPdf'
        ));
    }

    private function sanitizePlainText(?string $value): string
    {
        return trim(strip_tags(HtmlSanitizer::clean($value)));
    }
}
