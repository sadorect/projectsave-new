<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Exam;
use Illuminate\Support\Facades\DB;

$email = 'asom_test_user@example.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "Test user not found: {$email}\n";
    exit(1);
}

echo "User id={$user->id} email={$user->email}\n";

$courses = Course::all();
echo "Total courses: " . $courses->count() . "\n";
foreach ($courses as $c) {
    echo "- Course id={$c->id} title={$c->title} slug={$c->slug} status={$c->status}\n";
    $exams = $c->exams()->get();
    echo "  Exams: " . $exams->count() . "\n";
    foreach ($exams as $e) {
        echo "    - Exam id={$e->id} title={$e->title} is_active={$e->is_active} passing_score={$e->passing_score}\n";
    }
    $lessons = $c->lessons()->count();
    echo "  Lessons: {$lessons}\n";
}

// Check pivot
$pivot = DB::table('course_user')->where('user_id', $user->id)->get();
echo "Pivot rows for user: " . $pivot->count() . "\n";
foreach ($pivot as $row) {
    echo "- course_user: course_id={$row->course_id} status={$row->status} enrolled_at={$row->enrolled_at} completed_at={$row->completed_at}\n";
}

// Check certificates
$certs = $user->certificates()->get();
echo "Certificates for user: " . $certs->count() . "\n";
foreach ($certs as $cert) {
    echo "- Cert id={$cert->id} course_id={$cert->course_id} is_approved={$cert->is_approved}\n";
}

?>
