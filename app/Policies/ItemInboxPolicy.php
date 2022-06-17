<?php

namespace App\Policies;

use App\Models\ItemInbox;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemInboxPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function view(User $user, ItemInbox $itemInbox)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function create(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function update(User $user, ItemInbox $itemInbox)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function delete(User $user, ItemInbox $itemInbox)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function restore(User $user, ItemInbox $itemInbox)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function forceDelete(User $user, ItemInbox $itemInbox)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }
}
