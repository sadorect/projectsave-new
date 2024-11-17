<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\PrayerForceController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\VideoReelController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\NewsUpdateController;
use App\Http\Controllers\Admin\AdminPartnerController;
use App\Http\Controllers\Admin\AdminPrayerForceController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\Admin\NotificationSettingsController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');

// Blog Routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('posts.show');
Route::get('/feed', [FeedController::class, 'index'])->name('feed');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Event Routes
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Contact Routes
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Partnership Routes
Route::get('/partners/{type}', [PartnerController::class, 'create'])->name('partners.create');
Route::post('/partners/{type}', [PartnerController::class, 'store'])->name('partners.store');
Route::get('/partners/{type}/{partner}', [PartnerController::class, 'show'])->name('partners.show');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/user/profile', [UserDashboardController::class, 'profile'])->name('user.profile');
    Route::get('/user/partnerships', [UserDashboardController::class, 'partnerships'])->name('user.partnerships');
    Route::get('/user/notifications', [UserDashboardController::class, 'notifications'])->name('user.notifications');
    Route::get('/user/settings', [UserDashboardController::class, 'settings'])->name('user.settings');
    Route::patch('/user/profile', [UserDashboardController::class, 'updateProfile'])->name('user.profile.update');
    Route::patch('/user/preferences', [UserDashboardController::class, 'updatePreferences'])->name('user.preferences.update');
});
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function() {
    // Public admin routes
    Route::get('/login', [AdminController::class, 'loginForm'])->name('admin.login.form');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    
    // Protected admin routes
    Route::middleware(['admin'])->group(function() {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::resource('posts', PostController::class)->names('admin.posts');
        Route::resource('events', AdminEventController::class)->names('admin.events');
        Route::resource('categories', CategoryController::class)->names('admin.categories');
        Route::resource('tags', TagController::class)->names('admin.tags');
        Route::resource('news', NewsUpdateController::class);
        Route::resource('videos', VideoReelController::class);
        
        // User Management
        Route::resource('users', AdminUserController::class)->names('admin.users');
        
        // Partner Management
        Route::get('/partners', [AdminPartnerController::class, 'index'])->name('admin.partners.index');
        Route::get('/partners/{partner}', [AdminPartnerController::class, 'show'])->name('admin.partners.show');
        Route::patch('/partners/{partner}/approve', [AdminPartnerController::class, 'approve'])->name('admin.partners.approve');
        Route::patch('/partners/{partner}/reject', [AdminPartnerController::class, 'reject'])->name('admin.partners.reject');
        
        // Prayer Force Management
        Route::get('/prayer-force', [AdminPrayerForceController::class, 'index'])->name('admin.prayer-force.index');
        Route::get('/prayer-force/{partner}', [AdminPrayerForceController::class, 'show'])->name('admin.prayer-force.show');
        Route::patch('/prayer-force/{partner}/status', [AdminPrayerForceController::class, 'updateStatus'])->name('admin.prayer-force.status.update');
        
        // Settings
        Route::get('/notification-settings', [NotificationSettingsController::class, 'edit'])->name('admin.notification-settings.edit');
        Route::patch('/notification-settings', [NotificationSettingsController::class, 'update'])->name('admin.notification-settings.update');
        Route::get('/notification-settings/event-reminders', [NotificationSettingsController::class, 'editEventReminders'])
            ->name('admin.notification-settings.event-reminders');
        Route::patch('/notification-settings/event-reminders', [NotificationSettingsController::class, 'updateEventReminders'])
            ->name('admin.notification-settings.event-reminders.update');
        Route::get('/notification-settings/reminder-logs', [NotificationSettingsController::class, 'viewReminderLogs'])
            ->name('admin.notification-settings.reminder-logs');
        Route::post('/admin/notification-settings/event-reminders/send/{event}', 
        [NotificationSettingsController::class, 'sendManualReminder'])
        ->name('admin.notification-settings.event-reminders.send');

        Route::get('/admin/notification-settings/event-reminders/preview/{event}', 
            [NotificationSettingsController::class, 'previewReminder'])
            ->name('admin.notification-settings.event-reminders.preview');
        });
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/celebrations/logs', [AdminController::class, 'viewWishLogs'])
        ->name('admin.celebrations.logs');
        });

        
        Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
            Route::get('/dashboard/celebrants', [AdminController::class, 'showCelebrants'])
                ->name('admin.dashboard.celebrants');
        });
        

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/celebrations/statistics', [AdminController::class, 'celebrationStats'])
        ->name('admin.celebrations.statistics');
});




Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/celebrations/calendar', [AdminController::class, 'celebrationCalendar'])
        ->name('admin.celebrations.calendar');
});

});
require __DIR__.'/auth.php';
