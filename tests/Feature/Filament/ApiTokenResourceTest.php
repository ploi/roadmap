<?php

use Livewire\Livewire;
use App\Enums\UserRole;
use App\Models\ApiToken;
use App\Filament\Resources\ApiTokens\Pages\CreateApiToken;
use App\Filament\Resources\ApiTokens\Pages\ListApiTokens;

it('lets admins create an api token that only stores a hash', function () {
    createAndLoginUser(['role' => UserRole::Admin]);

    Livewire::test(CreateApiToken::class)
        ->fillForm(['name' => 'Polder'])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified(trans('resources.api-token.created-title'));

    $token = ApiToken::first();

    expect($token->name)->toBe('Polder')
        ->and($token->getAttributes()['token'])->toHaveLength(64);
});

it('is not available to employees', function () {
    createAndLoginUser(['role' => UserRole::Employee]);

    Livewire::test(ListApiTokens::class)->assertForbidden();
    Livewire::test(CreateApiToken::class)->assertForbidden();
});
