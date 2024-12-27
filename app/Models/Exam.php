<?php

namespace App\Models;

use App\Models\Course;
use App\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'course_id', 
        'title', 
        'description', 
        'duration_minutes', 
        'passing_score',
        'max_attempts',
        'allow_retakes',
        'is_active'  // Add this new field
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Scope for active exams (has 5+ questions OR marked active)
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                    ->orWhereHas('questions', function($q) {
                        $q->havingRaw('COUNT(*) >= 5');
                    });
    }

    // Check if exam is available for a specific student
    public function isAvailableForStudent(User $student)
    {
        return $this->course->isCompletedByStudent($student) &&
               ($this->is_active || $this->questions()->count() >= 5);
    }
}
