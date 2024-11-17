<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

class WebhookClient
{
    protected PendingRequest $client;

    public function __construct(public string $url)
    {
        $this->buildClient();
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param string $method
     * @param array<string, mixed> $data
     * @return mixed
     */
    public function send(string $method = 'get', array $data = []): mixed
    {
        return $this->client->{$method}($this->url, $data);
    }

    public function buildClient(): static
    {
        $this->client = Http::baseUrl($this->url)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);

        if (app()->isLocal()) {
            $this->client->withoutVerifying();
        }

        return $this;
    }
}
