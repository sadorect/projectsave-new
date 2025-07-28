<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'user_id',
        'course_id',
        'final_grade',
        'issued_at',
        'completed_at',
        'is_approved',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_approved' => 'boolean',
        'final_grade' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->certificate_id)) {
                $certificate->certificate_id = 'ASOM-' . strtoupper(Str::random(8)) . '-' . date('Y');
            }
        });
    }

    /**
     * Get the user who earned this certificate
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for this certificate (nullable for diploma certificates)
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if this is a diploma certificate (covers all courses)
     */
    public function isDiplomaCertificate(): bool
    {
        return is_null($this->course_id);
    }

    /**
     * Get certificate type display name
     */
    public function getCertificateTypeAttribute(): string
    {
        return $this->isDiplomaCertificate() ? 'Diploma in Ministry' : $this->course->title;
    }

    /**
     * Get the admin who approved this certificate
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if certificate is verified/approved
     */
    public function isVerified(): bool
    {
        return $this->is_approved;
    }

    /**
     * Approve the certificate
     */
    public function approve(User $approver): void
    {
        $this->update([
            'is_approved' => true,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);
    }

    /**
     * Get verification URL
     */
    public function getVerificationUrlAttribute(): string
    {
        return route('certificates.verify', $this->certificate_id);
    }
}
