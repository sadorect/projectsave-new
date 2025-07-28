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
use App\Http\Controllers\LMS\StudentExamController;
use App\Http\Controllers\LMS\CertificateController;


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



// Student Exam Routes (using StudentExamController)
Route::prefix('exams')->name('lms.exams.')->middleware(['auth'])->group(function () {
    Route::get('/', [StudentExamController::class, 'index'])->name('index');
    Route::get('/{exam}', [StudentExamController::class, 'show'])->name('show');
    Route::post('/{exam}/start', [StudentExamController::class, 'start'])->name('start');
    Route::get('/{exam}/attempts/{attempt}/take', [StudentExamController::class, 'take'])->name('take');
    Route::post('/{exam}/attempts/{attempt}/save-answer', [StudentExamController::class, 'saveAnswer'])->name('save-answer');
    Route::post('/{exam}/attempts/{attempt}/submit', [StudentExamController::class, 'submit'])->name('submit');
    Route::get('/{exam}/attempts/{attempt}/results', [StudentExamController::class, 'results'])->name('results');
});

// Certificate routes
Route::middleware(['auth'])->prefix('learn')->group(function () {
    Route::prefix('certificates')->name('lms.certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
        Route::post('/courses/{course}/generate', [CertificateController::class, 'generate'])->name('generate');
        Route::get('/{certificate}/download', [CertificateController::class, 'download'])->name('download');
    });
});

// Public certificate verification (no auth required)
Route::get('/verify/{certificateId}', [CertificateController::class, 'verify'])->name('certificates.verify');

