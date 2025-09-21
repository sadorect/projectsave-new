<?php
namespace App\Models;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'slug',
        'instructor_id',
        'featured_image',
        'status',
        'objectives',
        'outcomes',
        'evaluation',
        'recommended_books',
        'documents'
        
    ];

    protected $casts = [
        'documents' => 'json'
    ];
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function getFeaturedImageAttribute($value)
{
    if ($value) {
        return $value;
    }
    return null;
}

public function users()
{
    return $this->belongsToMany(User::class, 'course_user')
                ->withTimestamps()
                ->withPivot('status', 'enrolled_at', 'completed_at');
}

    public function getProgressAttribute()
    {
        if (!auth()->check()) {
            return 0;
        }
        
        $totalLessons = $this->lessons()->count();
        if ($totalLessons === 0) {
            return 0;
        }
        
        $completedLessons = $this->lessons()
            ->whereHas('progress', function($query) {
                $query->where('user_id', auth()->id())
                    ->where('completed', true);
            })
            ->count();
            
        return ($completedLessons / $totalLessons) * 100;
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function isCompleted()
    {
        return $this->progress === 100;
    }

    public function isCompletedByStudent(User $student)
    {
        // Compute progress specifically for the provided student instead of relying on auth()
        return $this->getProgressFor($student) === 100;
    }

    /**
     * Compute course progress (percentage) for a specific student.
     */
    public function getProgressFor(User $student)
    {
        if (!$student || !$student->id) {
            return 0;
        }

        $totalLessons = $this->lessons()->count();
        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = $this->lessons()
            ->whereHas('progress', function($query) use ($student) {
                $query->where('user_id', $student->id)
                    ->where('completed', true);
            })
            ->count();

        return ($completedLessons / $totalLessons) * 100;
    }
    
    public function isAvailableForStudent(User $student)
{
    return $this->course->users()
                ->where('user_id', $student->id)
                ->wherePivot('completed_at', '!=', null)
                ->exists() &&
           ($this->is_active || $this->questions()->count() >= 5);
}

    /**
     * Get the course featured image URL with fallback
     */
    public function getFeaturedImageUrlAttribute(): string
    {
        if ($this->featured_image) {
            return $this->featured_image;
        }
        
        return asset('frontend/img/course-placeholder.jpg');
    }

    /**
     * Check if course has a valid featured image
     */
    public function hasFeaturedImage(): bool
    {
        return !empty($this->featured_image);
    }

}




