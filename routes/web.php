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
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\PrayerForceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\VideoReelController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\NewsUpdateController;
use App\Http\Controllers\Admin\AdminPartnerController;
use App\Http\Controllers\Admin\AdminPrayerForceController;
use App\Http\Controllers\Admin\NotificationSettingsController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');

Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('posts.show');
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

//Admin Routes
Route::group(['prefix' => 'admin'], function() {
    Route::get('/login', [AdminController::class, 'loginForm'])->name('admin.login.form');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    
    // Protected admin routes
    Route::group(['middleware' => 'admin'], function() {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // Posts
        Route::resource('posts', PostController::class)->names('admin.posts');
        
        // Events
        Route::resource('events', AdminEventController::class)->names('admin.events');
        
        // Categories
        Route::resource('categories', CategoryController::class)->names('admin.categories');
        
        // Tags
        Route::resource('tags', TagController::class)->names('admin.tags');
    });
});
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function() {
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});


Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('news', NewsUpdateController::class);
    Route::resource('videos', VideoReelController::class);
    Route::post('videos/reorder', [VideoReelController::class, 'reorder'])->name('videos.reorder');
    Route::get('news/create', [NewsUpdateController::class, 'create'])->name('news.create');
    Route::get('videos/create', [VideoReelController::class, 'create'])->name('videos.create');
    Route::get('/prayer-force', [AdminPrayerForceController::class, 'index'])->name('admin.prayer-force.index');
    Route::patch('/prayer-force/{partner}/status', [AdminPrayerForceController::class, 'updateStatus'])
    ->name('admin.prayer-force.status.update');
    Route::get('/prayer-force/{partner}', [AdminPrayerForceController::class, 'show'])
    ->name('admin.prayer-force.show');

    Route::get('/notification-settings', [NotificationSettingsController::class, 'edit'])
        ->name('admin.notification-settings.edit');
    
    Route::patch('/notification-settings', [NotificationSettingsController::class, 'update'])
        ->name('admin.notification-settings.update');

    Route::patch('/prayer-force/{partner}/approve', [AdminPrayerForceController::class, 'approve'])
        ->name('admin.prayer-force.approve');
    
    Route::patch('/prayer-force/{partner}/reject', [AdminPrayerForceController::class, 'reject'])
        ->name('admin.prayer-force.reject');
});


Route::get('/volunteer/prayer-force', [PrayerForceController::class, 'index'])->name('volunteer.prayer-force');
Route::post('/partners/prayer-force', [PrayerForceController::class, 'store'])->name('partners.prayer-force.store');



Route::get('/partners/{type}',  [PartnerController::class, 'create'])->name('volunteer.prayer');

Route::get('/partners/{type}',  [PartnerController::class, 'create'])->name('volunteer.skilled');

Route::get('/partners/{type}',  [PartnerController::class, 'create'])->name('volunteer.ground');






Route::get('/partners/{type}/{partner}', [PartnerController::class, 'show'])
    ->name('partners.show');
Route::get('/partners/{type}', [PartnerController::class, 'create'])->name('partners.create');
Route::post('/partners/{type}', [PartnerController::class, 'store'])->name('partners.store');

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/partners', [AdminPartnerController::class, 'index'])->name('admin.partners.index');
    Route::get('/partners/{partner}', [AdminPartnerController::class, 'show'])->name('admin.partners.show');
    Route::patch('/partners/{partner}/approve', [AdminPartnerController::class, 'approve'])->name('admin.partners.approve');
    Route::patch('/partners/{partner}/reject', [AdminPartnerController::class, 'reject'])->name('admin.partners.reject');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');

Route::get('feed', [FeedController::class, 'index'])->name('feed');
