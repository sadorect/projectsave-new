<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

/**
 * Determines if the given user is authorized to update the specified course.
 *
 * @param \App\Models\User $user The user to check authorization for.
 * @param \App\Models\Course $course The course to check authorization for.
 * @return bool True if the user is authorized to update the course, false otherwise.
 */
public function update(User $user, Course $course)
{
    // Allow admins to update any course, or course instructors to update their own
    return $user->isAdmin() || $user->id === $course->instructor_id;
}

/**
 * Determines if the given user is authorized to delete the specified course.
 *
 * @param \App\Models\User $user The user to check authorization for.
 * @param \App\Models\Course $course The course to check authorization for.
 * @return bool True if the user is authorized to delete the course, false otherwise.
 */
public function delete(User $user, Course $course)
{
    // Allow admins to delete any course, or course instructors to delete their own
    return $user->isAdmin() || $user->id === $course->instructor_id;
}

}
