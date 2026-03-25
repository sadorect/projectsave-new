<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormPolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['manage-forms']);
    }

    public function create(User $user): bool
    {
        return $this->canAny($user, ['manage-forms']);
    }

    public function update(User $user, Form $form): bool
    {
        return $this->canAny($user, ['manage-forms']);
    }

    public function delete(User $user, Form $form): bool
    {
        return $this->canAny($user, ['manage-forms']);
    }

    public function viewSubmissions(User $user, Form $form): bool
    {
        return $this->canAny($user, ['manage-forms']);
    }

    public function export(User $user, Form $form): bool
    {
        return $this->canAny($user, ['manage-forms']);
    }
}