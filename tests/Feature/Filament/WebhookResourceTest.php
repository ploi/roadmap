<?php

use Livewire\Livewire;
use App\Enums\UserRole;
use App\Models\WebhookEndpoint;
use App\Filament\Resources\Webhooks\Pages\CreateWebhook;

it('lets admins create a webhook with a secret and events', function () {
    createAndLoginUser(['role' => UserRole::Admin]);

    Livewire::test(CreateWebhook::class)
        ->fillForm(['url' => 'https://example.com/hook', 'secret' => 'abc123', 'events' => ['item.created']])
        ->call('create')
        ->assertHasNoFormErrors();

    $endpoint = WebhookEndpoint::first();

    expect($endpoint->url)->toBe('https://example.com/hook')
        ->and($endpoint->secret)->toBe('abc123')
        ->and($endpoint->events)->toBe(['item.created']);
});

it('requires events', function () {
    createAndLoginUser(['role' => UserRole::Admin]);

    Livewire::test(CreateWebhook::class)
        ->fillForm(['url' => 'https://example.com/hook', 'secret' => 'abc123', 'events' => []])
        ->call('create')
        ->assertHasFormErrors(['events' => 'required']);
});
