<?php

// One-off manual test script to exercise verification and admin actions.
// Run: php scripts/manual_test.php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Certificate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "Starting manual tests...\n";

// 1) Create or find a test user
$email = 'verify_test+manual@example.com';
$user = User::where('email', $email)->first();
if (!$user) {
    $user = User::create([
        'name' => 'Manual Test User',
        'email' => $email,
        'password' => Hash::make('secret123'),
    ]);
    echo "Created user id={$user->id}\n";
} else {
    echo "Found existing user id={$user->id}\n";
}

// Ensure user initially unverified
// Some installations may not have an `is_active` column -- guard against that.
if (Illuminate\Support\Facades\Schema::hasColumn('users', 'is_active')) {
    $user->update(['email_verified_at' => null, 'is_active' => 0]);
    echo "User email_verified_at reset and is_active set to false.\n";
} else {
    $user->update(['email_verified_at' => null]);
    echo "User email_verified_at reset. 'is_active' column not present; skipped setting it.\n";
}

// 2) Simulate clicking verification link while unauthenticated
$hash = sha1($user->getEmailForVerification());
echo "Computed hash: $hash\n";

// Validate hash same as controller
if (hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
    echo "Hash validated for user id={$user->id}\n";
    if ($user->markEmailAsVerified()) {
        echo "markEmailAsVerified() succeeded. email_verified_at={$user->email_verified_at}\n";
    } else {
        echo "markEmailAsVerified() returned false (maybe already verified).\n";
    }
} else {
    echo "Hash validation failed.\n";
}

// Refresh user
$user->refresh();

// 3) Test admin verify controller action by calling it directly
echo "Testing AdminUserController@verify...\n";
$adminController = new App\Http\Controllers\AdminUserController();
$request = new Illuminate\Http\Request();
$response = $adminController->verify($request, $user);
echo "Admin verify invoked. Current email_verified_at={$user->fresh()->email_verified_at}\n";

// 4) Test toggleActive
echo "Testing AdminUserController@toggleActive...\n";
if (Illuminate\Support\Facades\Schema::hasColumn('users', 'is_active')) {
    $before = $user->fresh()->is_active;
    $adminController->toggleActive($request, $user);
    $after = $user->fresh()->is_active;
    echo "is_active before={$before} after={$after}\n";
} else {
    echo "Skipped toggleActive: 'is_active' column not present on users table.\n";
}

// 5) Simulate course completion -> certificate scenario (lightweight)
echo "Testing course progress -> certificate simulation...\n";

$course = Course::first();
if (!$course) {
    $course = Course::create(['title' => 'Manual Test Course', 'slug' => 'manual-test-course', 'description' => 'Test course', 'instructor_id' => $user->id]);
    echo "Created test course id={$course->id}\n";
}

// If the course exists but has no lessons, create a couple of lessons with required fields
if ($course->lessons()->count() === 0) {
    $l1 = Lesson::create(['course_id' => $course->id, 'title' => 'L1', 'slug' => 'l1', 'content' => 'Test content', 'instructor_id' => $user->id]);
    $l2 = Lesson::create(['course_id' => $course->id, 'title' => 'L2', 'slug' => 'l2', 'content' => 'Test content', 'instructor_id' => $user->id]);
    echo "Created test lessons for course id={$course->id}\n";
} else {
    echo "Course id={$course->id} already has {$course->lessons()->count()} lessons.\n";
}

// Enroll user to course pivot
$user->courses()->syncWithoutDetaching([$course->id => ['enrolled_at' => now(), 'status' => 'active']]);

// Mark lessons completed
foreach ($course->lessons as $lesson) {
    $user->lessonProgress()->updateOrCreate(['lesson_id' => $lesson->id, 'user_id' => $user->id], ['completed' => true, 'completed_at' => now()]);
}

echo "Marked lessons complete for user. progress={$user->getCourseProgress($course)}\n";

// Try to generate certificate by mimicking CertificateController logic
if (round($user->getCourseProgress($course), 2) >= 100) {
    $existing = Certificate::where('user_id', $user->id)->where('course_id', $course->id)->first();
    if (!$existing) {
        $cert = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'final_grade' => null,
            'issued_at' => now(),
            'completed_at' => now(),
            'is_approved' => false
        ]);
        echo "Created certificate id={$cert->id} certificate_id={$cert->certificate_id}\n";
    } else {
        echo "Existing certificate found id={$existing->id}\n";
    }
} else {
    echo "User progress insufficient to create certificate.\n";
}

echo "Manual tests complete.\n";
