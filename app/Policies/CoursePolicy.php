<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['view-courses', 'manage-courses']);
    }

    public function view(User $user, Course $course): bool
    {
        return $this->viewAny($user) || $user->id === $course->instructor_id;
    }

    public function create(User $user): bool
    {
        return $this->canAny($user, ['manage-courses']);
    }

    public function update(User $user, Course $course): bool
    {
        return $this->canAny($user, ['manage-courses']) || $user->id === $course->instructor_id;
    }

    public function delete(User $user, Course $course): bool
    {
        return $this->canAny($user, ['manage-courses']) || $user->id === $course->instructor_id;
    }
}
