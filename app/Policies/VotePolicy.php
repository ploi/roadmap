<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vote;
use Illuminate\Auth\Access\HandlesAuthorization;

class VotePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_EMPLOYEE);
    }

    public function view(User $user, Vote $model)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function create(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function update(User $user, Vote $model)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function delete(User $user, Vote $model)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function restore(User $user, Vote $model)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function forceDelete(User $user, Vote $model)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }
}
