<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserFile;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserFilePolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['manage-files']);
    }

    public function view(User $user, UserFile $file): bool
    {
        return $this->canAny($user, ['manage-files']);
    }

    public function delete(User $user, UserFile $file): bool
    {
        return $this->canAny($user, ['manage-files']);
    }

    public function manage(User $user): bool
    {
        return $this->canAny($user, ['manage-files']);
    }
}