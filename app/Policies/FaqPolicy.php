<?php

namespace App\Policies;

use App\Models\Faq;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class FaqPolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['view-faqs', 'access-content-admin', 'edit-content']);
    }

    public function view(User $user, Faq $faq): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->canAny($user, ['create-faqs', 'access-content-admin', 'edit-content']);
    }

    public function update(User $user, Faq $faq): bool
    {
        return $this->canAny($user, ['edit-faqs', 'access-content-admin', 'edit-content']);
    }

    public function delete(User $user, Faq $faq): bool
    {
        return $this->canAny($user, ['delete-faqs', 'access-content-admin', 'edit-content']);
    }

    public function bulkManage(User $user): bool
    {
        return $this->canAny($user, ['edit-faqs', 'delete-faqs', 'publish-faqs', 'access-content-admin', 'edit-content']);
    }
}