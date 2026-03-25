<?php

namespace App\Policies;

use App\Models\PrayerForcePartner;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrayerForcePartnerPolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['manage-prayer-force']);
    }

    public function view(User $user, PrayerForcePartner $partner): bool
    {
        return $this->viewAny($user);
    }

    public function moderate(User $user, PrayerForcePartner $partner): bool
    {
        return $this->canAny($user, ['manage-prayer-force']);
    }
}