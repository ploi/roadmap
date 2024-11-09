<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vote;
use App\Enums\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class VotePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::Admin, UserRole::Employee);
    }

    public function view(User $user, Vote $model): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function update(User $user, Vote $model): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function delete(User $user, Vote $model): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function restore(User $user, Vote $model): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function forceDelete(User $user, Vote $model): bool
    {
        return $user->hasRole(UserRole::Admin);
    }
}
