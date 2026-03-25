<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertificatePolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['manage-certificates', 'access-lms-admin']);
    }

    public function view(User $user, Certificate $certificate): bool
    {
        return $this->viewAny($user);
    }

    public function approve(User $user, Certificate $certificate): bool
    {
        return $this->canAny($user, ['manage-certificates', 'access-lms-admin']);
    }

    public function reject(User $user, Certificate $certificate): bool
    {
        return $this->canAny($user, ['manage-certificates', 'access-lms-admin']);
    }

    public function regenerate(User $user, Certificate $certificate): bool
    {
        return $this->canAny($user, ['manage-certificates', 'access-lms-admin']);
    }

    public function delete(User $user, Certificate $certificate): bool
    {
        return $this->canAny($user, ['manage-certificates', 'access-lms-admin']);
    }

    public function manage(User $user): bool
    {
        return $this->canAny($user, ['manage-certificates', 'access-lms-admin']);
    }
}