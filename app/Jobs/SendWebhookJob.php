<?php

namespace App\Jobs;

use App\Models\WebhookEndpoint;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public bool $deleteWhenMissingModels = true;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public readonly WebhookEndpoint $endpoint,
        public readonly string $event,
        public readonly array $data,
        public readonly string $deliveryId,
    ) {
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 60, 300, 900];
    }

    public function handle(): void
    {
        $timestamp = time();
        $body = json_encode([
            'event' => $this->event,
            'delivery_id' => $this->deliveryId,
            'created_at' => now()->toIso8601String(),
            'data' => $this->data,
        ]);

        // The signature covers the timestamp too, so receivers can reject replayed requests.
        $signature = hash_hmac('sha256', $timestamp . '.' . $body, $this->endpoint->secret);

        Http::withHeaders([
            'X-Roadmap-Event' => $this->event,
            'X-Roadmap-Delivery' => $this->deliveryId,
            'X-Roadmap-Timestamp' => (string) $timestamp,
            'X-Roadmap-Signature' => 'sha256=' . $signature,
        ])
            ->withBody($body, 'application/json')
            ->timeout(10)
            ->post($this->endpoint->url)
            ->throw();
    }
}
