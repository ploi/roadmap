<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRole;
use App\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::Admin, UserRole::Employee);
    }

    public function view(User $user, Project $project): bool
    {
        return $user->hasRole(UserRole::Admin, UserRole::Employee);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasRole(UserRole::Admin);
    }
}
