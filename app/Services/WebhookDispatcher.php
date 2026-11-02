<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Enums\WebhookEvent;
use App\Jobs\SendWebhookJob;
use App\Models\WebhookEndpoint;

class WebhookDispatcher
{
    /**
     * Queue the event for every active endpoint that subscribed to it.
     *
     * @param  array<string, mixed>  $data
     */
    public static function dispatch(WebhookEvent $event, array $data): void
    {
        WebhookEndpoint::query()
            ->where('active', true)
            ->get()
            ->filter(fn (WebhookEndpoint $endpoint) => in_array($event->value, $endpoint->events ?? [], true))
            ->each(fn (WebhookEndpoint $endpoint) => SendWebhookJob::dispatch($endpoint, $event->value, $data, (string) Str::uuid()));
    }
}
