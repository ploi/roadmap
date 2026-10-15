<?php

use Livewire\Livewire;
use App\Enums\UserRole;
use App\Filament\Pages\Settings;
use App\Settings\GeneralSettings;
use Filament\Actions\Testing\TestAction;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\PasskeysRelationManager;

test('passkeys are disabled by default', function () {
    expect(app(GeneralSettings::class)->enable_passkeys)->toBeFalse();
});

test('admins can enable passkeys in the settings', function () {
    createAndLoginUser(['role' => UserRole::Admin]);

    Livewire::test(Settings::class)
        ->fillForm(['enable_passkeys' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(GeneralSettings::class)->refresh()->enable_passkeys)->toBeTrue();
});

test('admins can revoke a users passkey', function () {
    GeneralSettings::fake(['enable_passkeys' => true]);
    createAndLoginUser(['role' => UserRole::Admin]);
    $user = createUser();
    $passkey = createPasskeyFor($user);

    Livewire::test(PasskeysRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->assertCanSeeTableRecords([$passkey])
        ->callAction(TestAction::make('delete')->table($passkey));

    $this->assertModelMissing($passkey);
});

test('the passkeys of a user stay revocable after passkeys got disabled', function () {
    GeneralSettings::fake(['enable_passkeys' => false]);
    $user = createUser();

    expect(PasskeysRelationManager::canViewForRecord($user, EditUser::class))->toBeFalse();

    createPasskeyFor($user);

    expect(PasskeysRelationManager::canViewForRecord($user, EditUser::class))->toBeTrue();
});
