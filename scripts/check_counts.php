<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'verify_test+manual@example.com')->first();
$course = App\Models\Course::first();

echo "user_id=" . ($user?->id ?? 'NULL') . "\n";
echo "course_id=" . ($course?->id ?? 'NULL') . "\n";
echo "lessons=" . ($course?->lessons()->count() ?? 'NULL') . "\n";
echo "lesson_progress=" . App\Models\LessonProgress::where('user_id', $user?->id ?? 0)->count() . "\n";
