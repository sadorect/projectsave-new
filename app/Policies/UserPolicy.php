<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['view-users', 'manage-users']);
    }

    public function view(User $user, User $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->canAny($user, ['create-users', 'manage-users']);
    }

    public function update(User $user, User $model): bool
    {
        return $this->canAny($user, ['edit-users', 'manage-users', 'manage-user-roles']);
    }

    public function delete(User $user, User $model): bool
    {
        return $this->canAny($user, ['delete-users', 'manage-users']);
    }

    public function bulkManage(User $user): bool
    {
        return $this->canAny($user, ['manage-users']);
    }

    public function verify(User $user, User $model): bool
    {
        return $this->canAny($user, ['verify-users', 'manage-users']);
    }

    public function toggleActive(User $user, User $model): bool
    {
        return $this->canAny($user, ['edit-users', 'manage-users']);
    }
}