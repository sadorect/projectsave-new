<?php

namespace App\Policies;

use App\Models\MailTemplate;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class MailTemplatePolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['manage-mail-templates']);
    }

    public function create(User $user): bool
    {
        return $this->canAny($user, ['manage-mail-templates']);
    }

    public function update(User $user, MailTemplate $mailTemplate): bool
    {
        return $this->canAny($user, ['manage-mail-templates']);
    }

    public function delete(User $user, MailTemplate $mailTemplate): bool
    {
        return $this->canAny($user, ['manage-mail-templates']);
    }
}