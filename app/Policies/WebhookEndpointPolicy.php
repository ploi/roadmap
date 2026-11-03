<?php

namespace App\Policies;

use App\Policies\Concerns\AdminsOnly;

/**
 * Webhooks send private data to external servers, so only admins may manage them.
 */
class WebhookEndpointPolicy
{
    use AdminsOnly;
}
