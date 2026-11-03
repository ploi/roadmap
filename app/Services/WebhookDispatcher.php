<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Str;
use App\Enums\WebhookEvent;
use App\Jobs\SendWebhookJob;
use App\Models\WebhookEndpoint;

class WebhookDispatcher
{
    /**
     * Queue the event for every active endpoint that subscribed to it.
     *
     * The payload is only built when at least one endpoint subscribed.
     *
     * @param  Closure(): array<string, mixed>  $data
     */
    public static function dispatch(WebhookEvent $event, Closure $data): void
    {
        $endpoints = WebhookEndpoint::query()
            ->where('active', true)
            ->get()
            ->filter(fn (WebhookEndpoint $endpoint) => in_array($event->value, $endpoint->events ?? [], true));

        if ($endpoints->isEmpty()) {
            return;
        }

        $payload = $data();
        $occurredAt = now()->toIso8601String();

        $endpoints->each(function (WebhookEndpoint $endpoint) use ($event, $payload, $occurredAt) {
            $pending = SendWebhookJob::dispatch($endpoint, $event->value, $payload, (string) Str::uuid(), $occurredAt);

            // On the sync queue delivery would otherwise block the user's request.
            if (config('queue.default') === 'sync') {
                $pending->afterResponse();
            }
        });
    }
}
