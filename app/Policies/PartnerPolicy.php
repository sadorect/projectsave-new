<?php

namespace App\Policies;

use App\Models\Partner;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class PartnerPolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['manage-partners']);
    }

    public function view(User $user, Partner $partner): bool
    {
        return $this->viewAny($user);
    }

    public function moderate(User $user, Partner $partner): bool
    {
        return $this->canAny($user, ['manage-partners']);
    }
}