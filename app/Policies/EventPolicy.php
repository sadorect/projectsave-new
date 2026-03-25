<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['view-events', 'access-content-admin', 'edit-content']);
    }

    public function view(User $user, Event $event): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->canAny($user, ['create-events', 'access-content-admin', 'edit-content']);
    }

    public function update(User $user, Event $event): bool
    {
        return $this->canAny($user, ['edit-events', 'access-content-admin', 'edit-content']);
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->canAny($user, ['delete-events', 'access-content-admin', 'edit-content']);
    }
}