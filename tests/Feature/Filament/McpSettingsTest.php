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

test('the settings show how to connect once mcp is enabled', function () {
    createAndLoginUser(['role' => UserRole::Admin]);

    Livewire::test(Settings::class)
        ->fillForm(['enable_mcp' => false])
        ->assertDontSeeText(trans('settings.mcp.connect-heading'))
        ->fillForm(['enable_mcp' => true])
        ->assertSeeText([trans('settings.mcp.connect-heading'), 'Claude Desktop', 'ChatGPT'])
        ->assertSee(route('mcp.docs'));
});

test('admins can allow regular users to create items through mcp', function () {
    createAndLoginUser(['role' => UserRole::Admin]);

    expect(app(GeneralSettings::class)->mcp_users_can_create_items)->toBeFalse();

    Livewire::test(Settings::class)
        ->fillForm(['enable_mcp' => true, 'mcp_users_can_create_items' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(GeneralSettings::class)->refresh()->mcp_users_can_create_items)->toBeTrue();
});
