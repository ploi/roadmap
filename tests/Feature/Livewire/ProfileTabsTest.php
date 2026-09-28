<?php

use Livewire\Livewire;
use App\Livewire\Profile;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\Select;

beforeEach(fn () => GeneralSettings::fake(['enable_mcp' => true]));

it('splits the profile into tabs', function () {
    createAndLoginUser();

    Livewire::test(Profile::class)
        ->assertSeeText(trans('profile.tabs.account'))
        ->assertSeeText(trans('profile.tabs.preferences'))
        ->assertSeeText(trans('profile.tabs.security'))
        ->assertSeeText(trans('profile.mcp.heading'))
        ->assertSeeText(trans('profile.two_factor.heading'))
        ->assertSeeText(trans('profile.delete-account'));
});

it('hides the mcp tab when mcp is disabled', function () {
    GeneralSettings::fake(['enable_mcp' => false]);

    createAndLoginUser();

    Livewire::test(Profile::class)
        ->assertSeeText(trans('profile.tabs.security'))
        ->assertDontSeeText(trans('profile.mcp.heading'));
});

it('saves fields from both the account and preferences tabs', function () {
    $user = createAndLoginUser();

    Livewire::test(Profile::class)
        ->fillForm([
            'name' => 'Jane Doe',
            'hide_from_leaderboard' => true,
        ])
        ->set('per_page_setting', ['10', '25'])
        ->call('submit')
        ->assertHasNoFormErrors()
        ->assertNotified('Profile');

    $user->refresh();

    expect($user->name)->toBe('Jane Doe')
        ->and($user->per_page_setting)->toEqual(['10', '25'])
        ->and($user->hide_from_leaderboard)->toBeTrue();
});

it('makes the locale fields searchable', function () {
    createAndLoginUser();

    Livewire::test(Profile::class)
        ->assertFormFieldExists('locale', fn (Select $field): bool => $field->isSearchable())
        ->assertFormFieldExists('date_locale', fn (Select $field): bool => $field->isSearchable());
});

it('renders action modals outside the social login table in its hidden tab', function () {
    config([
        'services.sso.url' => 'https://sso.example.com',
        'services.sso.client_id' => 'client-id',
        'services.sso.client_secret' => 'client-secret',
        'services.sso.redirect' => 'https://roadmap.test/oauth/callback',
    ]);

    createAndLoginUser();

    // Filament only renders the first modals container on the page, so it must come before the table's own one.
    Livewire::test(Profile::class)
        ->assertSeeHtmlInOrder(['filamentActionModals', 'filamentTable']);
});
