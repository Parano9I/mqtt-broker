<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Organization $organization): bool
    {
        return $user->role->isOwner();
    }

    public function update(User $user, Organization $organization): bool
    {
        return $user->role->isOwner() || $user->role->isAdmin();
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $user->role->isOwner();
    }
}
