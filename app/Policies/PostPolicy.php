<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use ChecksPermissions;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canAny($user, ['view-posts', 'access-content-admin', 'edit-content']);
    }

    public function view(User $user, Post $post): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->canAny($user, ['create-posts', 'access-content-admin', 'edit-content']);
    }

    public function update(User $user, Post $post): bool
    {
        return $this->canAny($user, ['edit-posts', 'access-content-admin', 'edit-content']);
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->canAny($user, ['delete-posts', 'access-content-admin', 'edit-content']);
    }

    public function bulkManage(User $user): bool
    {
        return $this->canAny($user, ['edit-posts', 'delete-posts', 'publish-posts', 'access-content-admin', 'edit-content']);
    }

    public function manageTaxonomy(User $user): bool
    {
        return $this->canAny($user, ['manage-post-taxonomy', 'access-content-admin', 'edit-content']);
    }
}