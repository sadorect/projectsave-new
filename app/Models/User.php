<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use App\Models\Course;
use App\Models\Partner;
use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
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
        'bio'
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
        'wedding_anniversary' => 'date'
    ];


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
                ->withPivot('status', 'completed_at');
}

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





}