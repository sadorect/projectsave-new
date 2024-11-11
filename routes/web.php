<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\PrayerForceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminEventController;


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

Route::get('/', function () {
    return view('home.index');
})->name('home');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
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

Route::get('/partners/prayer-force', [PrayerForceController::class, 'index'])->name('partners.prayer-force');
Route::post('/partners/prayer-force', [PrayerForceController::class, 'store'])->name('partners.prayer-force.store');
