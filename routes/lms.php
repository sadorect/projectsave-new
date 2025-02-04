<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LMS\ExamController;
use App\Http\Controllers\LMS\CourseController;
use App\Http\Controllers\LMS\LessonController;
use App\Http\Controllers\LMS\ProgressController;
use App\Http\Controllers\LMS\QuestionController;
use App\Http\Controllers\LMS\DashboardController;
use App\Http\Controllers\LMS\EnrollmentController;
use App\Http\Controllers\LMS\ExamAttemptController;
use App\Http\Controllers\LMS\LessonProgressController;


// Public access to courses
Route::get('/asom', [CourseController::class, 'landing'])->name('asom');
//Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('lms.courses.show');
// Course routes
Route::prefix('learn')->group(function() {
    Route::get('/', [CourseController::class, 'index'])->name('lms.courses.index');
    Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('lms.courses.show');
});
Route::middleware(['auth'])->prefix('learn')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('lms.dashboard');
    
    
    
    // Enrollment routes
    Route::post('/courses/{course:slug}/enroll', [DashboardController::class, 'enroll'])
        ->name('lms.courses.enroll');
    Route::delete('/courses/{course:slug}/unenroll', [DashboardController::class, 'unenroll'])
        ->name('lms.courses.unenroll');
    
    // Lesson routes
    Route::get('/courses/{course:slug}/lessons', [LessonController::class, 'index'])->name('lms.lessons.index');
    Route::get('/courses/{course:slug}/lessons/{lesson:slug}', [LessonController::class, 'show'])->name('lms.lessons.show');

    // Progress tracking routes
    Route::post('/courses/{course:slug}/lessons/{lesson:slug}/complete', [LessonProgressController::class, 'markComplete'])
        ->name('lessons.complete');
    
    Route::get('/courses/{course:slug}/progress', [LessonProgressController::class, 'getProgress'])
        ->name('courses.progress');
});



Route::prefix('lms')->name('lms.')->middleware(['auth'])->group(function () {
    Route::resource('exams', ExamController::class);
    Route::post('exams/{exam}/attempt', [ExamAttemptController::class, 'start'])->name('exams.attempt.start');
    Route::post('exams/{exam}/submit', [ExamAttemptController::class, 'submit'])->name('exams.attempt.submit');
    Route::get('exams/{exam}/results', [ExamAttemptController::class, 'results'])->name('exams.results');
    Route::get('exams/{exam}/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('exams/{exam}/questions', [QuestionController::class, 'store'])->name('questions.store');
});

