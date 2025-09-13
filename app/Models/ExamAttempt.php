<?php

namespace App\Models;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamAttempt extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'exam_id',
        'started_at',
        'completed_at',
        'score',
        'answers'
    ];
    
    protected $casts = [
        'answers' => 'array',
        'score' => 'float',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUserAndExam($query, $userId, $examId)
    {
        return $query->where('user_id', $userId)
                     ->where('exam_id', $examId);
    }

    /**
     * Check if this is a manual pass by admin
     */
    public function isManualPass(): bool
    {
        return isset($this->answers['manual_pass']) && $this->answers['manual_pass'] === true;
    }

    /**
     * Check if the student passed this exam
     */
    public function isPassed(): bool
    {
        return $this->score >= $this->exam->passing_score;
    }

    /**
     * Get the status of this attempt
     */
    public function getStatusAttribute(): string
    {
        return $this->isPassed() ? 'PASSED' : 'FAILED';
    }

    /**
     * Get admin notes if this is a manual pass
     */
    public function getAdminNotesAttribute(): ?string
    {
        return $this->answers['admin_notes'] ?? null;
    }

    /**
     * Get admin name who manually passed if applicable
     */
    public function getAdminNameAttribute(): ?string
    {
        return $this->answers['admin_name'] ?? null;
    }
}






