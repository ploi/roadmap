<?php

use Livewire\Livewire;
use App\Enums\UserRole;
use App\Filament\Pages\Settings;
use App\Settings\GeneralSettings;

test('mcp is disabled by default', function () {
    expect(app(GeneralSettings::class)->enable_mcp)->toBeFalse();
});

test('admins can enable mcp in the settings', function () {
    createAndLoginUser(['role' => UserRole::Admin]);

    Livewire::test(Settings::class)
        ->fillForm(['enable_mcp' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(GeneralSettings::class)->refresh()->enable_mcp)->toBeTrue();
});
