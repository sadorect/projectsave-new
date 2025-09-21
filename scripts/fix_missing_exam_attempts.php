<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;

$email = 'asom_test_user@example.com';
$user = User::where('email', $email)->first();
if (!$user) { echo "Test user not found\n"; exit(1); }

$asomCourseTitles = [
    'Bible Introduction',
    'Hermeneutics',
    'Ministry Vitals',
    'Spiritual Gifts & Ministry',
    'Biblical Counseling',
    'Homiletics'
];

$fixed = 0;
foreach ($asomCourseTitles as $title) {
    $course = Course::where('title', $title)->first();
    if (!$course) { echo "Course missing: {$title}\n"; continue; }
    foreach ($course->exams as $exam) {
        $exists = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->whereNotNull('completed_at')
            ->exists();
        if ($exists) {
            echo "User has attempt for exam {$exam->id} ({$exam->title})\n";
            continue;
        }
        // create a passing attempt
        $score = max(60, $exam->passing_score ?? 60);
        ExamAttempt::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now(),
            'score' => $score,
            'answers' => []
        ]);
        echo "Inserted passing attempt for exam {$exam->id} ({$exam->title}) score={$score}\n";
        $fixed++;
    }
}

echo "Done. Fixed: {$fixed}\n";

// Show diploma eligibility
$controller = new App\Http\Controllers\LMS\StudentExamController();
$res = $controller->checkDiplomaEligibility($user->id);
print_r($res);

?>
