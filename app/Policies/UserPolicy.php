<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function view(User $user, User $model)
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function create(User $user)
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function update(User $user, User $model)
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function delete(User $user, User $model)
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function restore(User $user, User $model)
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function forceDelete(User $user, User $model)
    {
        return $user->hasRole(UserRole::Admin);
    }
}
