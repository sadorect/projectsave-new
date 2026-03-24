<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Certificate;
use App\Models\Event;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Enrollment;
use App\Models\Faq;
use App\Models\Form;
use App\Models\Lesson;
use App\Models\MailTemplate;
use App\Models\Partner;
use App\Models\PrayerForcePartner;
use App\Models\Post;
use App\Models\User;
use App\Models\UserFile;
use App\Policies\CertificatePolicy;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\EventPolicy;
use App\Policies\ExamAttemptPolicy;
use App\Policies\ExamPolicy;
use App\Policies\FaqPolicy;
use App\Policies\FormPolicy;
use App\Policies\LessonPolicy;
use App\Policies\MailTemplatePolicy;
use App\Policies\PartnerPolicy;
use App\Policies\PostPolicy;
use App\Policies\PrayerForcePartnerPolicy;
use App\Policies\UserPolicy;
use App\Policies\UserFilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Certificate::class => CertificatePolicy::class,
        Course::class => CoursePolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
        Event::class => EventPolicy::class,
        Exam::class => ExamPolicy::class,
        ExamAttempt::class => ExamAttemptPolicy::class,
        Faq::class => FaqPolicy::class,
        Form::class => FormPolicy::class,
        Lesson::class => LessonPolicy::class,
        MailTemplate::class => MailTemplatePolicy::class,
        Partner::class => PartnerPolicy::class,
        PrayerForcePartner::class => PrayerForcePartnerPolicy::class,
        Post::class => PostPolicy::class,
        User::class => UserPolicy::class,
        UserFile::class => UserFilePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user) {
            if ($user->isAdmin() || $user->hasRole('admin')) {
                return true;
            }

            return null;
        });
    }
}
