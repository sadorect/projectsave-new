<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class LessonPolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['manage-lessons', 'manage-courses', 'access-lms-admin']);
    }

    public function create(User $user): bool
    {
        return $this->canAny($user, ['manage-lessons', 'manage-courses', 'access-lms-admin']);
    }

    public function update(User $user, Lesson $lesson): bool
    {
        return $this->canAny($user, ['manage-lessons', 'manage-courses', 'access-lms-admin']);
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $this->canAny($user, ['manage-lessons', 'manage-courses', 'access-lms-admin']);
    }
}