<?php

namespace App\Policies;

use App\Models\ExamAttempt;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamAttemptPolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['manage-exams', 'access-lms-admin']);
    }

    public function view(User $user, ExamAttempt $attempt): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, ExamAttempt $attempt): bool
    {
        return $this->canAny($user, ['manage-exams', 'access-lms-admin']);
    }

    public function manage(User $user): bool
    {
        return $this->canAny($user, ['manage-exams', 'access-lms-admin']);
    }
}