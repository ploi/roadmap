<?php

namespace App\Jobs;

use Throwable;
use RuntimeException;
use App\Rules\SafeWebhookUrl;
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
        public readonly string $occurredAt,
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
        try {
            $this->deliver();
        } catch (Throwable $exception) {
            // Without a real queue there is no retry, and a failing endpoint must not break the request.
            if (config('queue.default') !== 'sync') {
                throw $exception;
            }

            report($exception);
        }
    }

    private function deliver(): void
    {
        // Checked again on every attempt, DNS may have changed since the endpoint was saved.
        $address = SafeWebhookUrl::resolveAddress($this->endpoint->url)
            ?? throw new RuntimeException('The webhook URL is not allowed.');

        $timestamp = time();
        $body = json_encode([
            'event' => $this->event,
            'delivery_id' => $this->deliveryId,
            'created_at' => $this->occurredAt,
            'data' => $this->data,
        ], JSON_THROW_ON_ERROR);

        // The signature covers the timestamp too, so receivers can reject replayed requests.
        $signature = hash_hmac('sha256', $timestamp . '.' . $body, $this->endpoint->secret);

        $host = parse_url($this->endpoint->url, PHP_URL_HOST);
        $port = parse_url($this->endpoint->url, PHP_URL_PORT) ?: 443;

        Http::withHeaders([
            'X-Roadmap-Event' => $this->event,
            'X-Roadmap-Delivery' => $this->deliveryId,
            'X-Roadmap-Timestamp' => (string) $timestamp,
            'X-Roadmap-Signature' => 'sha256=' . $signature,
        ])
            ->withBody($body, 'application/json')
            ->withoutRedirecting()
            // Connect to the address we just checked, so DNS rebinding cannot swap it afterwards.
            ->withOptions(['curl' => [CURLOPT_RESOLVE => ["{$host}:{$port}:{$address}"]]])
            ->timeout(10)
            ->post($this->endpoint->url)
            ->throw();
    }
}
