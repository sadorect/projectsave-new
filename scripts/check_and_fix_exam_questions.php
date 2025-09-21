<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;

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

echo "Checking exams and question counts for user {$user->email}\n";
$included = 0;
foreach ($asomCourseTitles as $title) {
    $course = Course::where('title', $title)->first();
    if (!$course) { echo "- Course missing: {$title}\n"; continue; }
    $exams = $course->exams()->withCount('questions')->get();
    foreach ($exams as $exam) {
        $qcount = $exam->questions_count ?? $exam->questions()->count();
        $passes = $qcount >= 5 ? 'YES' : 'NO';
        echo "- {$title}: exam={$exam->id} title='{$exam->title}' questions={$qcount} passes>=5? {$passes}\n";
        if ($qcount >= 5) $included++;
        // If fewer than 5, create missing simple questions
        if ($qcount < 5 && class_exists(Question::class)) {
            $need = 5 - $qcount;
            for ($i = 1; $i <= $need; $i++) {
                Question::create([
                    'exam_id' => $exam->id,
                    'question_text' => "Auto Q {$i} for {$exam->title}",
                    'options' => ['A' => 'Option A', 'B' => 'Option B', 'C' => 'Option C'],
                    'correct_answer' => 'A',
                    'points' => 1
                ]);
            }
            echo "  -> Created {$need} questions to reach 5 for exam {$exam->id}\n";
        }
    }
}

echo "Summary: exams with >=5 questions: {$included}\n";

// Re-run check
$included2 = 0;
foreach ($asomCourseTitles as $title) {
    $course = Course::where('title', $title)->first();
    if (!$course) continue;
    $exams = $course->exams()->withCount('questions')->get();
    foreach ($exams as $exam) {
        $qcount = $exam->questions_count ?? $exam->questions()->count();
        if ($qcount >= 5) $included2++;
    }
}

echo "After fix: exams with >=5 questions: {$included2}\n";

?>
