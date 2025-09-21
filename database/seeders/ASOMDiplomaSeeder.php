<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Hash;

class ASOMDiplomaSeeder extends Seeder
{
    /**
     * Seed ASOM Diploma courses, lessons, exams, and a test user.
     */
    public function run()
    {
        $asomCourseTitles = [
            'Bible Introduction',
            'Hermeneutics',
            'Ministry Vitals',
            'Spiritual Gifts & Ministry',
            'Biblical Counseling',
            'Homiletics'
        ];

        // Create or find a test user
        $email = 'asom_test_user@example.com';
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'ASOM Test User',
                'password' => Hash::make('TestPass123!'),
                'email_verified_at' => now()
            ]
        );

        foreach ($asomCourseTitles as $title) {
            $course = Course::firstOrCreate(
                ['title' => $title],
                ['slug' => \Illuminate\Support\Str::slug($title), 'description' => $title . ' for Diploma', 'instructor_id' => $user->id, 'status' => 'published']
            );

            // Ensure course is published (update existing records)
            if ($course->status !== 'published') {
                $course->update(['status' => 'published']);
            }

            // Ensure there are 2 lessons at least
            if ($course->lessons()->count() === 0) {
                for ($i = 1; $i <= 2; $i++) {
                    $lessonData = [
                        'course_id' => $course->id,
                        'title' => "{$title} - Lesson {$i}",
                        'slug' => strtolower(str_replace(' ', '-', $title)) . "-l{$i}",
                        'content' => 'Auto-generated lesson content',
                        'order' => $i
                    ];

                    if (\Illuminate\Support\Facades\Schema::hasColumn('lessons', 'instructor_id')) {
                        $lessonData['instructor_id'] = $user->id;
                    }

                    Lesson::create($lessonData);
                }
            }

            // Create a simple exam with 5 questions (if missing)
            if ($course->exams()->count() === 0) {
                $exam = Exam::create([
                    'course_id' => $course->id,
                    'title' => $title . ' Exam',
                    'is_active' => true,
                    'duration_minutes' => 30,
                    'max_attempts' => 3,
                    'allow_retakes' => true,
                    'passing_score' => 50
                ]);

                // Create 5 simple questions with 1 point each (if Question model/table exists)
                if (class_exists(\App\Models\Question::class)) {
                    for ($q = 1; $q <= 5; $q++) {
                        Question::create([
                            'exam_id' => $exam->id,
                            'question_text' => "Sample question {$q} for {$title}",
                            'options' => ['A' => 'Option A', 'B' => 'Option B', 'C' => 'Option C'],
                            'correct_answer' => 'A',
                            'points' => 1
                        ]);
                    }
                }
            }

            // Enroll the user if not already
            $user->courses()->syncWithoutDetaching([$course->id => ['enrolled_at' => now(), 'status' => 'active']]);

            // Mark all lessons complete for the user
            foreach ($course->lessons as $lesson) {
                $user->lessonProgress()->updateOrCreate(
                    ['lesson_id' => $lesson->id, 'user_id' => $user->id],
                    ['completed' => true, 'completed_at' => now()]
                );
            }

            // Create a passing ExamAttempt for the user for the course's exam(s)
            foreach ($course->exams as $exam) {
                // Determine a passing score (use exam.passing_score)
                $score = max(60, $exam->passing_score);
                ExamAttempt::create([
                    'user_id' => $user->id,
                    'exam_id' => $exam->id,
                    'started_at' => now()->subMinutes(10),
                    'completed_at' => now(),
                    'score' => $score,
                    'answers' => []
                ]);
            }
        }

        $this->command->info("ASOM Diploma test user created: {$email} (password: TestPass123!)");
    }
}
