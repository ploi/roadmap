<?php

use Livewire\Livewire;
use App\Enums\UserRole;
use App\Models\WebhookEndpoint;
use App\Filament\Resources\Webhooks\Pages\CreateWebhook;
use App\Filament\Resources\Webhooks\Pages\EditWebhook;
use App\Filament\Resources\Webhooks\Pages\ListWebhooks;

it('lets admins create a webhook with a secret and events', function () {
    createAndLoginUser(['role' => UserRole::Admin]);

    Livewire::test(CreateWebhook::class)
        ->fillForm(['url' => 'https://1.1.1.1/hook', 'secret' => 'abc123', 'events' => ['item.created']])
        ->call('create')
        ->assertHasNoFormErrors();

    $endpoint = WebhookEndpoint::first();

    expect($endpoint->url)->toBe('https://1.1.1.1/hook')
        ->and($endpoint->secret)->toBe('abc123')
        ->and($endpoint->events)->toBe(['item.created']);
});

it('requires events', function () {
    createAndLoginUser(['role' => UserRole::Admin]);

    Livewire::test(CreateWebhook::class)
        ->fillForm(['url' => 'https://1.1.1.1/hook', 'secret' => 'abc123', 'events' => []])
        ->call('create')
        ->assertHasFormErrors(['events' => 'required']);
});

it('rejects urls that are not https or not public', function (string $url) {
    createAndLoginUser(['role' => UserRole::Admin]);

    Livewire::test(CreateWebhook::class)
        ->fillForm(['url' => $url, 'secret' => 'abc123', 'events' => ['item.created']])
        ->call('create')
        ->assertHasFormErrors(['url']);
})->with(['http://1.1.1.1/hook', 'https://127.0.0.1/hook', 'https://169.254.169.254/latest', 'https://localhost/hook']);

it('keeps the secret when an admin edits a webhook', function () {
    createAndLoginUser(['role' => UserRole::Admin]);
    $endpoint = WebhookEndpoint::factory()->create(['secret' => 'abc123']);

    Livewire::test(EditWebhook::class, ['record' => $endpoint->getKey()])
        ->assertSchemaStateSet(['secret' => 'abc123'])
        ->fillForm(['active' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($endpoint->refresh()->active)->toBeFalse()->and($endpoint->secret)->toBe('abc123');
});

it('is not available to employees', function () {
    createAndLoginUser(['role' => UserRole::Employee]);

    Livewire::test(ListWebhooks::class)->assertForbidden();
});
