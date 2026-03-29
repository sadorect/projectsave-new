<?php

use App\Http\Controllers\Admin\AdminFileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MinistryReportController;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\AsomPageSettingsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\PrayerForceController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\AiImageSettingsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LMS\ExamController;
use App\Http\Controllers\Admin\VideoReelController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\MinistryReportController as AdminMinistryReportController;
use App\Http\Controllers\Admin\NewsUpdateController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminLessonController;
use App\Http\Controllers\Admin\AdminPartnerController;
use App\Http\Controllers\Admin\LMS\QuestionController;
use App\Http\Controllers\Admin\MailTemplateController;
use App\Http\Controllers\Admin\AdminEnrollmentController;
use App\Http\Controllers\Admin\DeletionRequestController;
use App\Http\Controllers\Admin\LMS\ExamAttemptController;
use App\Http\Controllers\Admin\AdminPrayerForceController;
use App\Http\Controllers\Admin\NotificationSettingsController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\FormController;
use App\Services\MathCaptcha;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');

// Blog Routes
Route::get('/devotional', [BlogController::class, 'index'])->name('blog.index');
Route::get('/devotional/{post:slug}', [BlogController::class, 'show'])->name('posts.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.filter');
Route::get('/blog/calendar/{year}/{month}', [BlogController::class, 'getCalendarData'])->name('blog.calendar');
Route::get('/feed', [FeedController::class, 'index'])->name('feed');
Route::get('/search', [SearchController::class, 'index'])->name('search');

        // Add this route for public FAQ display
 Route::get('/faqs/{faqs:slug}', [FaqController::class, 'show'])->name('faqs.show');
 Route::get('/faqs', [FaqController::class, 'list'])->name('faqs.list');

// Event Routes
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');
Route::get('/reports', [MinistryReportController::class, 'index'])->name('reports.index');
Route::get('/reports/{report:slug}', [MinistryReportController::class, 'show'])->name('reports.show');

// Contact Routes
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit')->middleware('throttle:10,1');
Route::post('/newsletter/subscribe', [NewsletterSubscriptionController::class, 'store'])->name('newsletter.subscribe')->middleware('throttle:5,1');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterSubscriptionController::class, 'destroy'])->name('newsletter.unsubscribe');
Route::get('/captcha/math', fn () => response()->json(MathCaptcha::generate()))->name('math-captcha.refresh');

// Prayer Force volunteer routes
Route::get('/volunteer/prayer-force', [PrayerForceController::class, 'index'])->name('volunteer.prayer-force');
Route::post('/volunteer/prayer-force', [PrayerForceController::class, 'store'])->name('volunteer.prayer-force.store')->middleware('throttle:5,1');
Route::get('/volunteer/prayer-force/{partner}', [PrayerForceController::class, 'show'])->name('volunteer.prayer-force.show');

// Public Certificate Verification Route
Route::get('/certificates/verify/{certificateId}', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'verify'])->name('certificates.public.verify');

// Partnership Routes
Route::get('/partners/{type}', [PartnerController::class, 'create'])->name('partners.create');
Route::post('/partners/{type}', [PartnerController::class, 'store'])->name('partners.store')->middleware('throttle:5,1');
Route::get('/partners/{type}/{partner}', [PartnerController::class, 'show'])->name('partners.show');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
//Auth::routes(['verify' => true]);
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('account')->name('user.')->group(function () {
        Route::get('/', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
        Route::get('/partnerships', [UserDashboardController::class, 'partnerships'])->name('partnerships');
        Route::get('/notifications', [UserDashboardController::class, 'notifications'])->name('notifications');
        Route::get('/settings', [UserDashboardController::class, 'settings'])->name('settings');
        Route::patch('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::patch('/preferences', [UserDashboardController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('/deletion', [UserDashboardController::class, 'showDeletionForm'])->name('account.deletion');
        Route::post('/deletion', [UserDashboardController::class, 'requestDeletion'])->name('account.deletion.request');

        Route::middleware('permission:manage-files,admin')->get('/files', [UserDashboardController::class, 'files'])->name('files');
    });

    Route::patch('/notification-preferences', [UserDashboardController::class, 'updatePreferences'])
        ->name('notification-preferences.update');

    Route::redirect('/user/dashboard', '/account', 301);
    Route::redirect('/user/profile', '/account/profile', 301);
    Route::redirect('/user/partnerships', '/account/partnerships', 301);
    Route::redirect('/user/notifications', '/account/notifications', 301);
    Route::redirect('/user/settings', '/account/settings', 301);
    Route::redirect('/user/files', '/account/files', 301);

    // File Management Routes
    Route::middleware('permission:manage-files,admin')->prefix('files')->name('files.')->group(function () {
        Route::get('/', [FileManagerController::class, 'index'])->name('index');
        Route::post('/upload', [FileManagerController::class, 'upload'])->name('upload');
        Route::get('/{file}/download', [FileManagerController::class, 'download'])->name('download');
        Route::delete('/{file}', [FileManagerController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function() {
    // Authentication Routes
    Route::get('/login', [AdminController::class, 'loginForm'])->name('admin.login.form');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login')->middleware('throttle:5,1');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('/open', [AdminController::class, 'open'])->middleware(['auth', 'verified'])->name('admin.open');
    
    // Permission-aware Admin Routes
    Route::middleware(['auth', 'verified', 'permission:access-admin-dashboard,admin'])->group(function() {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/dashboard/celebrants', [AdminController::class, 'showCelebrants'])->name('admin.dashboard.celebrants');
        Route::post('/dashboard/celebrants/{userId}/wish', [AdminController::class, 'sendWishes'])->middleware('permission:view-reports,admin')->name('admin.dashboard.send-wishes');
             
        Route::middleware('permission:manage-files,admin')->prefix('files')->name('admin.files.')->group(function () {
            Route::get('/', [AdminFileController::class, 'index'])->name('index');
            Route::get('/analysis', [AdminFileController::class, 'storageAnalysis'])->name('analysis');
            Route::get('/{file}', [AdminFileController::class, 'show'])->name('show');
            Route::get('/{file}/download', [AdminFileController::class, 'download'])->name('download');
            Route::delete('/{file}', [AdminFileController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [AdminFileController::class, 'bulkDelete'])->name('bulk-delete');
            Route::patch('/{file}/privacy', [AdminFileController::class, 'updatePrivacy'])->name('update-privacy');
            Route::post('/cleanup-expired', [AdminFileController::class, 'cleanupExpired'])->name('cleanup-expired');
        });

        // User Management — explicit non-{user} routes MUST come before the resource
        Route::middleware('permission:manage-users,view-users,create-users,edit-users,delete-users,verify-users,manage-user-roles,admin')->group(function () {
            Route::post('users/bulk-action', [\App\Http\Controllers\AdminUserController::class, 'bulkAction'])->name('admin.users.bulk-action');
            Route::resource('users', AdminUserController::class)->names('admin.users');
            Route::patch('users/{user}/verify', [\App\Http\Controllers\AdminUserController::class, 'verify'])->name('admin.users.verify');
            Route::patch('users/{user}/toggle-active', [\App\Http\Controllers\AdminUserController::class, 'toggleActive'])->name('admin.users.toggle-active');
        });
        Route::middleware('permission:manage-user-roles,view-roles,create-roles,edit-roles,delete-roles,manage-roles,admin')->group(function () {
            Route::get('roles', [RoleController::class, 'index'])->name('admin.roles.index');
            Route::get('roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
            Route::post('roles', [RoleController::class, 'store'])->name('admin.roles.store');
            Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
            Route::put('roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
            Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
            Route::get('permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
            Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('admin.permissions.edit');
        });
        Route::middleware('permission:manage-user-roles,manage-roles,admin')->group(function () {
            Route::post('permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');
            Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('admin.permissions.update');
            Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('admin.permissions.destroy');
        });
        Route::middleware('permission:manage-user-sessions,admin')->group(function () {
            Route::get('sessions', [\App\Http\Controllers\Admin\AdminSessionController::class, 'index'])->name('admin.sessions.index');
            Route::delete('sessions/{session}', [\App\Http\Controllers\Admin\AdminSessionController::class, 'destroy'])->name('admin.sessions.destroy');
        });
        Route::middleware('permission:view-audit-log,manage-audit-log,admin')->group(function () {
            Route::get('audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('admin.audit.index');
        });

        Route::middleware('permission:manage-audit-log,admin')->group(function () {
            Route::delete('audit-logs/{id}', [\App\Http\Controllers\Admin\AuditLogController::class, 'destroy'])->name('admin.audit.destroy');
            Route::post('audit-logs/bulk-delete', [\App\Http\Controllers\Admin\AuditLogController::class, 'bulkDestroy'])->name('admin.audit.bulkDestroy');
            Route::post('audit-logs/toggle', [\App\Http\Controllers\Admin\AuditLogController::class, 'toggleErrorAudit'])->name('admin.audit.toggle');
        });
        Route::middleware('permission:manage-settings,admin')->group(function () {
            Route::get('/site-settings', [SiteSettingsController::class, 'edit'])->name('admin.site-settings.edit');
            Route::patch('/site-settings', [SiteSettingsController::class, 'update'])->name('admin.site-settings.update');
        });
        Route::middleware('permission:manage-users,admin')->group(function () {
            Route::get('/deletion-requests', [DeletionRequestController::class, 'index'])->name('admin.deletion-requests.index');
            Route::get('/deletion-requests/{request}', [DeletionRequestController::class, 'show'])->name('admin.deletion-requests.show');
            Route::post('/deletion-requests/{request}/process', [DeletionRequestController::class, 'process'])->name('admin.deletion-requests.process');
        });
        
        Route::middleware('permission:manage-partners,admin')->group(function () {
            Route::get('/partners', [AdminPartnerController::class, 'index'])->name('admin.partners.index');
            Route::get('/partners/{partner}', [AdminPartnerController::class, 'show'])->name('admin.partners.show');
            Route::patch('/partners/{partner}/approve', [AdminPartnerController::class, 'approve'])->name('admin.partners.approve');
            Route::patch('/partners/{partner}/reject', [AdminPartnerController::class, 'reject'])->name('admin.partners.reject');
        });
        
        Route::middleware('permission:manage-prayer-force,admin')->group(function () {
            Route::get('/prayer-force', [AdminPrayerForceController::class, 'index'])->name('admin.prayer-force.index');
            Route::get('/prayer-force/{partner}', [AdminPrayerForceController::class, 'show'])->name('admin.prayer-force.show');
            Route::patch('/prayer-force/{partner}/approve', [AdminPrayerForceController::class, 'approve'])->name('admin.prayer-force.approve');
            Route::patch('/prayer-force/{partner}/reject', [AdminPrayerForceController::class, 'reject'])->name('admin.prayer-force.reject');
            Route::patch('/prayer-force/{partner}/status', [AdminPrayerForceController::class, 'updateStatus'])->name('admin.prayer-force.status.update');
        });
        
        Route::middleware('permission:view-reports,admin')->group(function () {
            Route::get('/celebrations/logs', [AdminController::class, 'viewWishLogs'])->name('admin.celebrations.logs');
            Route::get('/celebrations/statistics', [AdminController::class, 'celebrationStats'])->name('admin.celebrations.statistics');
            Route::get('/celebrations/calendar', [AdminController::class, 'celebrationCalendar'])->name('admin.celebrations.calendar');
        });
        
        Route::middleware('permission:manage-notification-settings,admin')->group(function () {
            Route::get('/notification-settings', [NotificationSettingsController::class, 'edit'])->name('admin.notification-settings.edit');
            Route::patch('/notification-settings', [NotificationSettingsController::class, 'update'])->name('admin.notification-settings.update');
            Route::get('/notification-settings/event-reminders', [NotificationSettingsController::class, 'editEventReminders'])->name('admin.notification-settings.event-reminders');
            Route::patch('/notification-settings/event-reminders', [NotificationSettingsController::class, 'updateEventReminders'])->name('admin.notification-settings.event-reminders.update');
            Route::get('/notification-settings/reminder-logs', [NotificationSettingsController::class, 'viewReminderLogs'])->name('admin.notification-settings.reminder-logs');
            Route::post('/notification-settings/event-reminders/send/{event}', [NotificationSettingsController::class, 'sendManualReminder'])->name('admin.notification-settings.event-reminders.send');
            Route::get('/notification-settings/event-reminders/preview/{event}', [NotificationSettingsController::class, 'previewReminder'])->name('admin.notification-settings.event-reminders.preview');
        });

        Route::middleware('permission:manage-forms,admin')->group(function () {
            Route::get('/forms', [FormController::class, 'adminIndex'])->name('admin.forms.index');
            Route::get('/forms/create', [FormController::class, 'create'])->name('admin.forms.create');
            Route::get('/forms/{form}/edit', [FormController::class, 'edit'])->name('admin.forms.edit');
            Route::post('/forms', [FormController::class, 'store'])->name('admin.forms.store');
            Route::put('/forms/{form}', [FormController::class, 'update'])->name('admin.forms.update');
            Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('admin.forms.destroy');
            Route::get('/forms/{form}/submissions', [FormController::class, 'submissions'])->name('admin.forms.submissions');
            Route::get('/forms/{form}/download', [FormController::class, 'downloadSubmissions'])->name('admin.forms.download');
            Route::get('/submissions', [FormController::class, 'submissionsIndex'])->name('admin.submissions.index');
        });
    });
});


// Content Management
Route::prefix('content')->middleware(['auth', 'verified'])->group(function() {
    Route::middleware('permission:view-posts,create-posts,edit-posts,delete-posts,publish-posts,manage-post-taxonomy,access-content-admin,edit-content,admin')->group(function() {
        Route::get('ai-images/settings', [AiImageSettingsController::class, 'edit'])->name('admin.ai-images.settings.edit');
        Route::post('ai-images/settings', [AiImageSettingsController::class, 'update'])->name('admin.ai-images.settings.update');
        Route::post('ai-images/settings/{provider}/test', [AiImageSettingsController::class, 'testProvider'])->name('admin.ai-images.settings.test');
        Route::resource('posts', PostController::class)->names('admin.posts');
        // Add new routes for enhanced post management
        Route::post('posts/bulk-action', [PostController::class, 'bulkAction'])->name('admin.posts.bulk-action');
        Route::post('posts/create-category', [PostController::class, 'createCategory'])->name('admin.posts.create-category');
        Route::post('posts/{post}/generate-featured-image', [PostController::class, 'generateFeaturedImage'])->name('admin.posts.generate-featured-image');
        Route::post('posts/{post}/approve-featured-image', [PostController::class, 'approveFeaturedImage'])->name('admin.posts.approve-featured-image');
        Route::post('posts/{post}/reject-featured-image', [PostController::class, 'rejectFeaturedImage'])->name('admin.posts.reject-featured-image');
    });

    Route::middleware('permission:view-events,create-events,edit-events,delete-events,publish-events,access-content-admin,edit-content,admin')->group(function() {
        Route::resource('events', AdminEventController::class)->names('admin.events');
    });

    Route::middleware('permission:manage-post-taxonomy,access-content-admin,edit-content,admin')->group(function() {
        Route::resource('categories', CategoryController::class)->names('admin.categories');
        Route::resource('tags', TagController::class)->names('admin.tags');
    });

    Route::middleware('permission:access-content-admin,edit-content,admin')->group(function() {
        Route::resource('news', NewsUpdateController::class);
        Route::resource('videos', VideoReelController::class);
        Route::resource('reports', AdminMinistryReportController::class)->except('show')->names('admin.reports');
    });

    Route::middleware('permission:view-faqs,create-faqs,edit-faqs,delete-faqs,publish-faqs,access-content-admin,edit-content,admin')->group(function() {
        Route::resource('faqs', \App\Http\Controllers\FaqController::class)->names('admin.faqs');
        // Add bulk action route for FAQs
        Route::post('faqs/bulk-action', [\App\Http\Controllers\FaqController::class, 'bulkAction'])->name('admin.faqs.bulk-action');
    });
});

Route::middleware(['auth', 'verified', 'permission:access-admin-dashboard,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('permission:manage-mail,admin')->group(function () {
        Route::get('/mail/compose', [MailController::class, 'compose'])->name('mail.compose');
        Route::post('/mail/send', [MailController::class, 'send'])->name('mail.send');
        Route::post('/mail/preview/{template?}', [MailController::class, 'preview'])->name('mail.preview');
        Route::get('/newsletter-subscribers', [NewsletterSubscriberController::class, 'index'])->name('newsletter-subscribers.index');
        Route::get('/newsletter-subscribers/{newsletterSubscriber}', [NewsletterSubscriberController::class, 'show'])->name('newsletter-subscribers.show');
    });

    Route::middleware('permission:manage-mail-templates,admin')->group(function () {
        Route::resource('mail-templates', MailTemplateController::class);
    });
});

// Admin LMS Routes
Route::middleware(['auth', 'verified', 'permission:access-lms-admin,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('permission:manage-exams,admin')->group(function () {
        Route::get('exams/{exam}/import-questions', [ExamController::class, 'showImportForm'])->name('exams.import-questions');
        Route::post('exams/{exam}/import-questions', [ExamController::class, 'importDocx'])->name('exams.import-questions.upload');
        Route::post('exams/{exam}/import-preview', [ExamController::class, 'importPreview'])->name('exams.import-preview');
        Route::post('exams/{exam}/import-confirm', [ExamController::class, 'importConfirm'])->name('exams.import-confirm');
        Route::get('exams/{exam}/questions', [ExamController::class, 'questions'])->name('exams.questions.import');
        Route::resource('exams', ExamController::class);
        Route::get('exams/{exam}/preview', [ExamController::class, 'preview'])->name('exams.preview');
        Route::get('exams/{exam}/edit', [ExamController::class, 'edit'])->name('exams.edit');
        Route::patch('exams/{exam}/toggle-activation', [ExamController::class, 'toggleActivation'])->name('exams.toggle-activation');
        Route::post('exams/{exam}/attempt', [ExamAttemptController::class, 'start'])->name('exams.attempt.start');
        Route::get('exam-attempts', [ExamAttemptController::class, 'index'])->name('exam-attempts.index');
        Route::get('exam-attempts/{attempt}', [ExamAttemptController::class, 'show'])->name('exam-attempts.show');
        Route::delete('exam-attempts/{attempt}', [ExamAttemptController::class, 'destroy'])->name('exam-attempts.destroy');
        Route::get('exams/{exam}/manual-pass', [ExamAttemptController::class, 'manualPass'])->name('exams.manual-pass');
        Route::post('exams/{exam}/manual-pass', [ExamAttemptController::class, 'storeManualPass'])->name('exams.manual-pass.store');
        Route::post('exams/{exam}/reset-attempts/{user?}', [ExamAttemptController::class, 'resetAttempts'])->name('exams.reset-attempts');
        Route::post('exams/{exam}/submit', [ExamAttemptController::class, 'submit'])->name('exams.attempt.submit');
        Route::get('exams/{exam}/results', [ExamAttemptController::class, 'results'])->name('exams.results');
        Route::get('exams/{exam}/questions/create', [QuestionController::class, 'create'])->name('questions.create');
        Route::post('exams/{exam}/questions', [QuestionController::class, 'store'])->name('questions.store');
        Route::get('exams/{exam}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
        Route::put('exams/{exam}/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
        Route::delete('exams/{exam}/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    });

    Route::middleware('permission:manage-courses,admin')->group(function () {
        Route::get('asom-page', [AsomPageSettingsController::class, 'edit'])->name('asom-page.edit');
        Route::put('asom-page', [AsomPageSettingsController::class, 'update'])->name('asom-page.update');
        Route::resource('courses', AdminCourseController::class);
    });

    Route::middleware('permission:manage-lessons,admin')->group(function () {
        Route::resource('lessons', AdminLessonController::class);
    });

    Route::middleware('permission:manage-enrollments,admin')->group(function () {
        Route::get('enrollments', [AdminEnrollmentController::class, 'index'])->name('enrollments.index');
        Route::get('enrollments/create', [AdminEnrollmentController::class, 'create'])->name('enrollments.create');
        Route::delete('enrollments/{course}/{user}', [AdminEnrollmentController::class, 'destroy'])->name('enrollments.destroy');
        Route::patch('enrollments/{course}/{user}/status', [AdminEnrollmentController::class, 'updateStatus'])->name('enrollments.status');
        Route::post('enrollments/store', [AdminEnrollmentController::class, 'store'])->name('enrollments.store');
    });

    Route::middleware('permission:manage-certificates,admin')->group(function () {
        Route::prefix('certificates')->name('certificates.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'index'])->name('index');
            Route::get('/pending', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'pending'])->name('pending');
            Route::post('/scan-missing', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'scanMissing'])->name('scan-missing');
            Route::post('/generate-sample', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'generateSample'])->name('generate-sample');
            Route::post('/generate-sample-course', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'generateSampleCourse'])->name('generate-sample-course');
            Route::delete('/cleanup-samples', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'cleanupSamples'])->name('cleanup-samples');
            Route::get('/{certificate}', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'show'])->name('show');
            Route::get('/{certificate}/preview', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'preview'])->name('preview');
            Route::patch('/{certificate}/approve', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'approve'])->name('approve');
            Route::patch('/{certificate}/reject', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'reject'])->name('reject');
            Route::post('/{certificate}/regenerate', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'regenerate'])->name('regenerate');
            Route::delete('/{certificate}', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-approve', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'bulkApprove'])->name('bulk-approve');
            Route::get('/export', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'export'])->name('export');
        });

        Route::get('/certificate-settings', [\App\Http\Controllers\Admin\CertificateSettingsController::class, 'index'])->name('certificate-settings');
        Route::put('/certificate-settings', [\App\Http\Controllers\Admin\CertificateSettingsController::class, 'update'])->name('certificate-settings.update');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/lms.php';


Route::get('/blog/dates/{year}/{month}', [BlogController::class, 'getDates'])->name('blog.dates');





Route::get('/forms/{form}', [FormController::class, 'show'])->name('forms.show');
Route::post('/forms/{form}/submit', [FormController::class, 'submit'])->name('forms.submit');
