<?php

use App\Http\Controllers\LMS\CourseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('learn')->group(function () {
    // Public course routes
    Route::get('/', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
    
    // Protected course content routes
    Route::middleware(['course.access'])->group(function () {
        Route::get('/courses/{course:slug}/lessons', [LessonController::class, 'index'])->name('lessons.index');
        Route::get('/courses/{course:slug}/lessons/{lesson:slug}', [LessonController::class, 'show'])->name('lessons.show');
    });
});
