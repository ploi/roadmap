<?php

namespace App\Policies;

use App\Policies\Concerns\AdminsOnly;

/**
 * API tokens expose all private data, so only admins may manage them.
 */
class ApiTokenPolicy
{
    use AdminsOnly;
}
