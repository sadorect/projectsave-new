<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Course;
use App\Models\Partner;
use App\Models\Activity;
use App\Models\Lesson;
use App\Models\Role;
use App\Models\UserFile;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\VideoInteraction;
use Illuminate\Support\Arr;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use MustVerifyEmailTrait;
    use HasRoles {
        hasRole as protected spatieHasRole;
        hasPermissionTo as protected spatieHasPermissionTo;
    }

    protected string $guard_name = 'web';

    /**
      * The attributes that are mass assignable.
      *
      * @var array<int, string>
      */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'birthday',
        'wedding_anniversary',
        'bio',
        'user_type',
        'language',
        'timezone',
        'preferences',
        'notification_preferences',
    ];

    /**
     * Attributes that must never be mass-assigned (privilege escalation guard).
     *
     * @var array<int, string>
     */
    protected $guarded = [
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
        'preferences' => 'array',
        'notification_preferences' => 'array',
    ];

    protected function getDefaultGuardName(): string
    {
        return $this->guard_name;
    }

    public function videoInteractions()
    {
    return $this->hasMany(VideoInteraction::class);
    }
    
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

public function isAsomStudent(): bool
{
        return $this->user_type === 'asom_student';
}

public function canAccessAdminDashboard(): bool
{
    return $this->hasAnyBackofficeAbility(['access-admin-dashboard']);
}

public function canAccessContentAdmin(): bool
{
    return $this->hasAnyBackofficeAbility(['access-content-admin', 'edit-content']);
}

public function canAccessLmsAdmin(): bool
{
    return $this->hasAnyBackofficeAbility(['access-lms-admin']);
}

public function hasBackofficeAccess(): bool
{
    return $this->canAccessAdminDashboard()
        || $this->canAccessContentAdmin()
        || $this->canAccessLmsAdmin();
}

public function preferredBackofficeRoute(): ?string
{
    $routes = [
        'admin.dashboard' => ['access-admin-dashboard'],
        'admin.posts.index' => ['view-posts', 'create-posts', 'edit-posts', 'delete-posts', 'publish-posts', 'edit-content'],
        'admin.events.index' => ['view-events', 'create-events', 'edit-events', 'delete-events', 'publish-events'],
        'admin.faqs.index' => ['view-faqs', 'create-faqs', 'edit-faqs', 'delete-faqs', 'publish-faqs'],
        'admin.categories.index' => ['manage-post-taxonomy', 'edit-content'],
        'news.index' => ['access-content-admin', 'edit-content'],
        'admin.files.index' => ['manage-files'],
        'admin.users.index' => ['manage-users', 'view-users', 'create-users', 'edit-users', 'delete-users', 'verify-users', 'manage-user-roles'],
        'admin.sessions.index' => ['manage-user-sessions'],
        'admin.audit.index' => ['view-audit-log', 'manage-audit-log'],
        'admin.partners.index' => ['manage-partners'],
        'admin.prayer-force.index' => ['manage-prayer-force'],
        'admin.celebrations.statistics' => ['view-reports'],
        'admin.notification-settings.edit' => ['manage-notification-settings'],
        'admin.forms.index' => ['manage-forms'],
        'admin.mail.compose' => ['manage-mail'],
        'admin.newsletter-subscribers.index' => ['manage-mail'],
        'admin.mail-templates.index' => ['manage-mail-templates'],
        'admin.courses.index' => ['manage-courses'],
        'admin.lessons.index' => ['manage-lessons'],
        'admin.enrollments.index' => ['manage-enrollments'],
        'admin.exams.index' => ['manage-exams'],
        'admin.certificates.index' => ['manage-certificates'],
    ];

    foreach ($routes as $route => $abilities) {
        if ($this->hasAnyBackofficeAbility($abilities)) {
            return $route;
        }
    }

    return null;
}

public function dashboardRoute(): string
{
    if ($route = $this->preferredBackofficeRoute()) {
        return $route;
    }

    if ($this->isAsomStudent()) {
        return 'asom.welcome';
    }

    return 'user.dashboard';
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



public function hasRole($role, ?string $guard = null): bool
{
    try {
        if ($this->spatieHasRole($role, $guard)) {
            return true;
        }
    } catch (\Illuminate\Database\QueryException) {
        // Spatie role tables may not exist; fall through to legacy role lookup.
    }

    $roles = $this->relationLoaded('roles') ? $this->roles : $this->roles()->get();

    foreach (Arr::wrap($role) as $value) {
        if (is_string($value) && $roles->contains('slug', $value)) {
            return true;
        }

        if ($value instanceof Role && $roles->contains('id', $value->getKey())) {
            return true;
        }
    }

    return false;
}

public function hasPermission($permission): bool
{
    try {
        if ($this->spatieHasPermissionTo($permission)) {
            return true;
        }
    } catch (PermissionDoesNotExist) {
        // Fall through to legacy slug lookup for transitional compatibility.
    } catch (\Illuminate\Database\QueryException) {
        // Spatie permission tables may not exist yet; fall through to legacy lookup.
    }

    if (! is_string($permission)) {
        return false;
    }

    return $this->getAllPermissions()->contains(function ($assignedPermission) use ($permission) {
        return $assignedPermission->slug === $permission || $assignedPermission->name === $permission;
    });
}

private function hasAnyBackofficeAbility(array $abilities): bool
{
    if ($this->isAdmin() || $this->hasRole('admin')) {
        return true;
    }

    foreach ($abilities as $ability) {
        if ($this->hasPermission($ability)) {
            return true;
        }
    }

    return false;
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
