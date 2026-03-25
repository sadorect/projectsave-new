<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class EnrollmentPolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['manage-enrollments', 'access-lms-admin']);
    }

    public function view(User $user, Enrollment $enrollment): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->canAny($user, ['manage-enrollments', 'access-lms-admin']);
    }

    public function manage(User $user): bool
    {
        return $this->canAny($user, ['manage-enrollments', 'access-lms-admin']);
    }
}