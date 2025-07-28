<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Course;
use App\Models\Partner;
use App\Models\Activity;
use App\Models\UserFile;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
      * The attributes that are mass assignable.
      *
      * @var array<int, string>
      */
    protected $fillable = [
        'name',
        'email',
        'password',
        'birthday',
        'wedding_anniversary',
        'bio',
        'user_type',
        'is_admin',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
        'wedding_anniversary' => 'date',
        'is_admin' => 'boolean',
    ];

    public function files()
    {
        return $this->hasMany(UserFile::class);
    }
    // In your User model
public function getTotalFileSizeAttribute()
{
    return $this->files()->sum('size');
}

public function getFormattedTotalFileSizeAttribute()
{
    return $this->formatBytes($this->total_file_size);
}

private function formatBytes($size, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

    public function isAdmin()
    {
        return $this->is_admin === true;
    }

public function activities()
{
    return $this->hasMany(Activity::class);
}



public function partnerships()
{
    return $this->hasMany(Partner::class);
}

public function partners()
{
    return $this->hasMany(Partner::class);
}



public function getNextCelebrationDateAttribute()
{
    $nextBirthday = $this->birthday ? Carbon::parse($this->birthday)->setYear(now()->year) : null;
    $nextAnniversary = $this->wedding_anniversary ? Carbon::parse($this->wedding_anniversary)->setYear(now()->year) : null;
    
    if ($nextBirthday && $nextBirthday->isPast()) {
        $nextBirthday->addYear();
    }
    
    if ($nextAnniversary && $nextAnniversary->isPast()) {
        $nextAnniversary->addYear();
    }
    
    if (!$nextBirthday && !$nextAnniversary) {
        return null;
    }
    
    if (!$nextBirthday) return $nextAnniversary;
    if (!$nextAnniversary) return $nextBirthday;
    
    return $nextBirthday->min($nextAnniversary);
}



public function roles()
{
    return $this->belongsToMany(Role::class);
}

public function hasRole($role)
{
    return $this->roles->contains('slug', $role);
}

public function hasPermission($permission)
{
    return $this->roles->flatMap->permissions->contains('slug', $permission);
}


public function enrollments()
{
    return $this->hasMany(Enrollment::class);
}

public function isEnrolledIn()
{
    return $this->hasMany(Enrollment::class);
}

public function courses()
{
    return $this->belongsToMany(Course::class, 'course_user')
                ->withTimestamps()
                ->withPivot('status', 'enrolled_at', 'completed_at');
}


 /**
     * Get the lesson progress for the user.
     */
    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }

public function markLessonAsCompleted(Lesson $lesson)
{
    return $this->lessonProgress()->updateOrCreate(
        ['lesson_id' => $lesson->id],
        ['completed' => true, 'last_accessed_at' => now()]
    );
}

public function getCourseProgress(Course $course)
{
    $totalLessons = $course->lessons()->count();
    if ($totalLessons === 0) return 0;

    $completedLessons = $this->lessonProgress()
        ->whereHas('lesson', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
        ->where('completed', true)
        ->count();

    return ($completedLessons / $totalLessons) * 100;
}

/**
 * Get the exam attempts for the user.
 */
public function examAttempts()
{
    return $this->hasMany(\App\Models\ExamAttempt::class);
}

/**
 * Get the certificates for the user.
 */
public function certificates()
{
    return $this->hasMany(\App\Models\Certificate::class);
}





}