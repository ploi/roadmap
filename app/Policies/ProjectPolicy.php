<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_EMPLOYEE);
    }

    public function view(User $user, Project $project)
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_EMPLOYEE);
    }

    public function create(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function update(User $user, Project $project)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function delete(User $user, Project $project)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function restore(User $user, Project $project)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function forceDelete(User $user, Project $project)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }
}
