<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LMS\CourseController;
use App\Http\Controllers\LMS\LessonController;
use App\Http\Controllers\LMS\ProgressController;
use App\Http\Controllers\LMS\DashboardController;
use App\Http\Controllers\LMS\EnrollmentController;

Route::middleware(['auth'])->prefix('learn')->group(function () {
    // Public course routes
    Route::get('/', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
    
    // Protected course content routes
    Route::middleware(['course.access'])->group(function () {
        Route::get('/courses/{course:slug}/lessons', [LessonController::class, 'index'])->name('lessons.index');
        Route::get('/courses/{course:slug}/lessons/{lesson:slug}', [LessonController::class, 'show'])->name('lessons.show');
    });

    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('lms.dashboard');

    Route::post('/lessons/{lesson}/complete', [ProgressController::class, 'markComplete'])
    ->name('lessons.complete');


    
     // Enrollment routes
     Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store'])->name('courses.enroll');
     Route::delete('/courses/{course}/unenroll', [EnrollmentController::class, 'destroy'])->name('courses.unenroll');
});