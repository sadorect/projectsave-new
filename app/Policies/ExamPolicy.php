<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['manage-exams', 'access-lms-admin']);
    }

    public function view(User $user, Exam $exam): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->canAny($user, ['manage-exams', 'access-lms-admin']);
    }

    public function update(User $user, Exam $exam): bool
    {
        return $this->canAny($user, ['manage-exams', 'access-lms-admin']);
    }

    public function delete(User $user, Exam $exam): bool
    {
        return $this->canAny($user, ['manage-exams', 'access-lms-admin']);
    }

    public function manage(User $user): bool
    {
        return $this->canAny($user, ['manage-exams', 'access-lms-admin']);
    }
}