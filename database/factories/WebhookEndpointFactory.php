<?php

namespace Database\Factories;

use App\Enums\WebhookEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WebhookEndpoint>
 */
class WebhookEndpointFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => 'https://example.com/hooks/roadmap',
            'secret' => $this->faker->sha256(),
            'events' => array_column(WebhookEvent::cases(), 'value'),
            'active' => true,
        ];
    }
}
