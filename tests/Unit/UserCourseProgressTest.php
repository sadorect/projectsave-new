<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserCourseProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_with_no_lessons_returns_zero()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $this->actingAs($user);

        $this->assertSame(0, $user->getCourseProgress($course));
    }

    public function test_user_with_all_lessons_completed_is_100_percent()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        // Create 3 lessons
        $lessons = Lesson::factory()->count(3)->create(['course_id' => $course->id]);

        // Mark each lesson as completed for user
        foreach ($lessons as $lesson) {
            $user->lessonProgress()->create([
                'lesson_id' => $lesson->id,
                'completed' => true,
                'completed_at' => now()
            ]);
        }

        $this->actingAs($user);

        $this->assertEquals(100, round($user->getCourseProgress($course), 2));
    }
}
