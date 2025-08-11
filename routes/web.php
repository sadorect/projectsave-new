<?php

use App\Http\Controllers\Admin\AdminFileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\PrayerForceController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LMS\ExamController;
use App\Http\Controllers\Admin\VideoReelController;
use App\Http\Controllers\Admin\AdminEventController;
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
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\Admin\NotificationSettingsController;
use App\Http\Controllers\FormController;
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

// Contact Routes
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Public Certificate Verification Route
Route::get('/certificates/verify/{certificateId}', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'verify'])->name('certificates.public.verify');

// Partnership Routes
Route::get('/partners/{type}', [PartnerController::class, 'create'])->name('partners.create');
Route::post('/partners/{type}', [PartnerController::class, 'store'])->name('partners.store');
Route::get('/partners/{type}/{partner}', [PartnerController::class, 'show'])->name('partners.show');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
//Auth::routes(['verify' => true]);
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/user/profile', [UserDashboardController::class, 'profile'])->name('user.profile');
    Route::get('/user/partnerships', [UserDashboardController::class, 'partnerships'])->name('user.partnerships');
    Route::get('/user/notifications', [UserDashboardController::class, 'notifications'])->name('user.notifications');
    Route::get('/user/settings', [UserDashboardController::class, 'settings'])->name('user.settings');
    Route::patch('/user/profile', [UserDashboardController::class, 'updateProfile'])->name('user.profile.update');
    Route::patch('/user/preferences', [UserDashboardController::class, 'updatePreferences'])->name('user.preferences.update');
    Route::get('/account/deletion', [UserDashboardController::class, 'showDeletionForm'])
        ->name('user.account.deletion');
    Route::post('/account/deletion', [UserDashboardController::class, 'requestDeletion'])
        ->name('user.account.deletion.request');

 // Add the files route
 Route::get('/user/files', [UserDashboardController::class, 'files'])->name('user.files');

    // File Management Routes
    Route::prefix('files')->name('files.')->group(function () {
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
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    
    // Protected Admin Routes
    Route::middleware(['auth', 'admin'])->group(function() {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/dashboard/celebrants', [AdminController::class, 'showCelebrants'])->name('admin.dashboard.celebrants');
             
        // File Management Routes
    Route::prefix('files')->name('admin.files.')->group(function ()
     {
        Route::get('/', [AdminFileController::class, 'index'])->name('index');
        Route::get('/analysis', [AdminFileController::class, 'storageAnalysis'])->name('analysis');
        Route::get('/{file}', [AdminFileController::class, 'show'])->name('show');
        Route::get('/{file}/download', [AdminFileController::class, 'download'])->name('download');
        Route::delete('/{file}', [AdminFileController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [AdminFileController::class, 'bulkDelete'])->name('bulk-delete');
        Route::patch('/{file}/privacy', [AdminFileController::class, 'updatePrivacy'])->name('update-privacy');
        Route::post('/cleanup-expired', [AdminFileController::class, 'cleanupExpired'])->name('cleanup-expired');
    });       

        // User Management
        Route::resource('users', AdminUserController::class)->names('admin.users');
        Route::get('/deletion-requests', [DeletionRequestController::class, 'index'])->name('admin.deletion-requests.index');
        Route::get('/deletion-requests/{request}', [DeletionRequestController::class, 'show'])->name('admin.deletion-requests.show');
        Route::post('/deletion-requests/{request}/process', [DeletionRequestController::class, 'process'])->name('admin.deletion-requests.process');
        
        // Partner Management
        Route::get('/partners', [AdminPartnerController::class, 'index'])->name('admin.partners.index');
        Route::get('/partners/{partner}', [AdminPartnerController::class, 'show'])->name('admin.partners.show');
        Route::patch('/partners/{partner}/approve', [AdminPartnerController::class, 'approve'])->name('admin.partners.approve');
        Route::patch('/partners/{partner}/reject', [AdminPartnerController::class, 'reject'])->name('admin.partners.reject');
        
        // Prayer Force Management
        Route::get('/prayer-force', [AdminPrayerForceController::class, 'index'])->name('admin.prayer-force.index');
        Route::get('/prayer-force/{partner}', [AdminPrayerForceController::class, 'show'])->name('admin.prayer-force.show');
        Route::patch('/prayer-force/{partner}/status', [AdminPrayerForceController::class, 'updateStatus'])->name('admin.prayer-force.status.update');
        
        // Celebrations Management
        Route::get('/celebrations/logs', [AdminController::class, 'viewWishLogs'])->name('admin.celebrations.logs');
        Route::get('/celebrations/statistics', [AdminController::class, 'celebrationStats'])->name('admin.celebrations.statistics');
        Route::get('/celebrations/calendar', [AdminController::class, 'celebrationCalendar'])->name('admin.celebrations.calendar');
        
        // Notification Settings
        Route::get('/notification-settings', [NotificationSettingsController::class, 'edit'])->name('admin.notification-settings.edit');
        Route::patch('/notification-settings', [NotificationSettingsController::class, 'update'])->name('admin.notification-settings.update');
        Route::get('/notification-settings/event-reminders', [NotificationSettingsController::class, 'editEventReminders'])->name('admin.notification-settings.event-reminders');
        Route::patch('/notification-settings/event-reminders', [NotificationSettingsController::class, 'updateEventReminders'])->name('admin.notification-settings.event-reminders.update');
        Route::get('/notification-settings/reminder-logs', [NotificationSettingsController::class, 'viewReminderLogs'])->name('admin.notification-settings.reminder-logs');
        Route::post('/notification-settings/event-reminders/send/{event}', [NotificationSettingsController::class, 'sendManualReminder'])->name('admin.notification-settings.event-reminders.send');
        Route::get('/notification-settings/event-reminders/preview/{event}', [NotificationSettingsController::class, 'previewReminder'])->name('admin.notification-settings.event-reminders.preview');
    });
});


// Content Management
Route::prefix('content')->middleware(['auth'])->group(function() {
    Route::middleware('permission:edit-content,admin')->group(function() {
        Route::resource('posts', PostController::class)->names('admin.posts');
        Route::resource('events', AdminEventController::class)->names('admin.events');
        Route::resource('categories', CategoryController::class)->names('admin.categories');
        Route::resource('tags', TagController::class)->names('admin.tags');
        Route::resource('news', NewsUpdateController::class);
        Route::resource('videos', VideoReelController::class);

         Route::resource('faqs', \App\Http\Controllers\FaqController::class)->names('admin.faqs');
    });
});

   // Admin LMS Routes
   Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // LMS Management

Route::get('exams/{exam}/import-questions', [ExamController::class, 'showImportForm'])->name('exams.import-questions');
Route::post('exams/{exam}/import-questions', [ExamController::class, 'importDocx'])->name('exams.import-questions.upload');
// Step 1: Preview
Route::post('exams/{exam}/import-preview', [ExamController::class, 'importPreview'])->name('exams.import-preview');

// Step 2: Confirm import
Route::post('exams/{exam}/import-confirm', [ExamController::class, 'importConfirm'])->name('exams.import-confirm');

Route::get('exams/{exam}/questions', [ExamController::class, 'questions'])->name('exams.questions.import');
    Route::resource('courses', AdminCourseController::class);
    Route::resource('lessons', AdminLessonController::class);
    Route::get('enrollments', [AdminEnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('enrollments/create', [AdminEnrollmentController::class, 'create'])->name('enrollments.create');
    Route::delete('enrollments/{course}/{user}', [AdminEnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    Route::patch('enrollments/{course}/{user}/status', [AdminEnrollmentController::class, 'updateStatus'])->name('enrollments.status');
 Route::get('enrollments/{course}', [AdminEnrollmentController::class, 'show'])->name('enrollments.show');
 Route::get('enrollments/{course}/edit', [AdminEnrollmentController::class, 'edit'])->name('enrollments.edit');
 Route::put('enrollments/{course}/{user}', [AdminEnrollmentController::class, 'update'])->name('enrollments.update');
 Route::post('enrollments/store', [AdminEnrollmentController::class, 'store'])->name('enrollments.store');

 Route::resource('exams', ExamController::class);
 Route::get('exams/{exam}/preview', [ExamController::class, 'preview'])->name('exams.preview');
 Route::get('exams/{exam}/edit', [ExamController::class, 'edit'])->name('exams.edit');
 //Route::post('exams/{exam}/update', [ExamController::class, 'update'])->name('exams.update');
 Route::patch('exams/{exam}/toggle-activation', [ExamController::class, 'toggleActivation'])->name('exams.toggle-activation');

    //Route::put('exams/{exam}', [ExamController::class, 'update'])->name('exams.update');
    Route::post('exams/{exam}/attempt', [ExamAttemptController::class, 'start'])->name('exams.attempt.start');
    Route::get('exam-attempts', [ExamAttemptController::class, 'index'])->name('exam-attempts.index');
    Route::post('exams/{exam}/reset-attempts/{user?}', [ExamAttemptController::class, 'resetAttempts'])->name('exams.reset-attempts');
    Route::post('exams/{exam}/submit', [ExamAttemptController::class, 'submit'])->name('exams.attempt.submit');
    Route::get('exams/{exam}/results', [ExamAttemptController::class, 'results'])->name('exams.results');
    Route::get('exams/{exam}/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('exams/{exam}/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::get('exams/{exam}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('exams/{exam}/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('exams/{exam}/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    
    // Certificate Management Routes
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'index'])->name('index');
        Route::get('/pending', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'pending'])->name('pending');
        Route::post('/generate-sample', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'generateSample'])->name('generate-sample');
        Route::post('/generate-sample-course', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'generateSampleCourse'])->name('generate-sample-course');
        Route::delete('/cleanup-samples', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'cleanupSamples'])->name('cleanup-samples');
        Route::get('/{certificate}', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'show'])->name('show');
        Route::patch('/{certificate}/approve', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'approve'])->name('approve');
        Route::patch('/{certificate}/reject', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'bulkApprove'])->name('bulk-approve');
        Route::get('/export', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'export'])->name('export');
    });

    Route::get('/mail/compose', [MailController::class, 'compose'])->name('mail.compose');
    Route::post('/mail/send', [MailController::class, 'send'])->name('mail.send');
    Route::resource('mail-templates', MailTemplateController::class);
    Route::post('/mail/preview/{template}', [MailController::class, 'preview'])->name('mail.preview');

// Form Routes


Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/forms', [FormController::class, 'adminIndex'])->name('forms.index');
    Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create');
    Route::get('/forms/{form}/edit', [FormController::class, 'edit'])->name('forms.edit');
    Route::post('/forms', [FormController::class, 'store'])->name('forms.store');
    Route::put('/forms/{form}', [FormController::class, 'update'])->name('forms.update');
    Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('forms.destroy');
    Route::get('/forms/{form}/submissions', [FormController::class, 'submissions'])->name('forms.submissions');
    Route::get('/forms/{form}/download', [FormController::class, 'downloadSubmissions'])->name('forms.download');
    Route::get('/submissions', [FormController::class, 'submissionsIndex'])->name('submissions.index');
});
});

require __DIR__.'/auth.php';
require __DIR__.'/lms.php';


Route::get('/blog/dates/{year}/{month}', [BlogController::class, 'getDates'])->name('blog.dates');





Route::get('/forms/{form}', [FormController::class, 'show'])->name('forms.show');
Route::post('/forms/{form}/submit', [FormController::class, 'submit'])->name('forms.submit');