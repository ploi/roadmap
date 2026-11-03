<?php

namespace App\Policies\Concerns;

use App\Models\User;
use App\Enums\UserRole;

/**
 * Filament only enforces a policy for the abilities it defines, so every ability is spelled out.
 */
trait AdminsOnly
{
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $user->hasRole(UserRole::Admin);
    }
}
