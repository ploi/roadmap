<?php

use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Profile;
use Laravel\Fortify\Fortify;
use PragmaRX\Google2FA\Google2FA;
use function Pest\Laravel\actingAs;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

test('a user can enable two-factor authentication with the correct password', function () {
    Livewire::test(Profile::class)
        ->callAction('enableTwoFactor', ['current_password' => 'password'])
        ->assertHasNoActionErrors();

    $this->user->refresh();

    expect($this->user->two_factor_secret)->not->toBeNull()
        ->and($this->user->two_factor_confirmed_at)->toBeNull();
});

test('enabling two-factor authentication requires the correct password', function () {
    Livewire::test(Profile::class)
        ->callAction('enableTwoFactor', ['current_password' => 'wrong-password'])
        ->assertHasActionErrors(['current_password']);

    expect($this->user->fresh()->two_factor_secret)->toBeNull();
});

test('a user can confirm two-factor authentication with a valid code', function () {
    app(EnableTwoFactorAuthentication::class)($this->user);
    $this->user->refresh();

    $secret = Fortify::currentEncrypter()->decrypt($this->user->two_factor_secret);

    Livewire::test(Profile::class)
        ->callAction('confirmTwoFactor', ['code' => (new Google2FA)->getCurrentOtp($secret)])
        ->assertHasNoActionErrors();

    expect($this->user->fresh()->two_factor_confirmed_at)->not->toBeNull();
});

test('confirming two-factor authentication rejects an invalid code', function () {
    app(EnableTwoFactorAuthentication::class)($this->user);
    $this->user->refresh();

    Livewire::test(Profile::class)
        ->callAction('confirmTwoFactor', ['code' => '000000'])
        ->assertHasActionErrors(['code']);

    expect($this->user->fresh()->two_factor_confirmed_at)->toBeNull();
});

test('a user can regenerate recovery codes', function () {
    app(EnableTwoFactorAuthentication::class)($this->user);
    $this->user->forceFill(['two_factor_confirmed_at' => now()])->save();
    $this->user->refresh();

    $original = $this->user->recoveryCodes();

    Livewire::test(Profile::class)
        ->callAction('regenerateRecoveryCodes')
        ->assertHasNoActionErrors();

    expect($this->user->fresh()->recoveryCodes())->not->toEqual($original);
});

test('a user can disable two-factor authentication with the correct password', function () {
    app(EnableTwoFactorAuthentication::class)($this->user);
    $this->user->forceFill(['two_factor_confirmed_at' => now()])->save();

    Livewire::test(Profile::class)
        ->callAction('disableTwoFactor', ['current_password' => 'password'])
        ->assertHasNoActionErrors();

    $this->user->refresh();

    expect($this->user->two_factor_secret)->toBeNull()
        ->and($this->user->two_factor_confirmed_at)->toBeNull();
});

test('disabling two-factor authentication requires the correct password', function () {
    app(EnableTwoFactorAuthentication::class)($this->user);
    $this->user->forceFill(['two_factor_confirmed_at' => now()])->save();

    Livewire::test(Profile::class)
        ->callAction('disableTwoFactor', ['current_password' => 'wrong-password'])
        ->assertHasActionErrors(['current_password']);

    expect($this->user->fresh()->two_factor_secret)->not->toBeNull();
});

test('the profile shows the enable option when two-factor is disabled', function () {
    Livewire::test(Profile::class)
        ->assertSee(trans('profile.two_factor.status_disabled'))
        ->assertSee(trans('profile.two_factor.enable'));
});

test('the profile shows recovery codes and management once two-factor is confirmed', function () {
    app(EnableTwoFactorAuthentication::class)($this->user);
    $this->user->forceFill(['two_factor_confirmed_at' => now()])->save();
    $this->user->refresh();

    Livewire::test(Profile::class)
        ->assertSee(trans('profile.two_factor.status_enabled'))
        ->assertSee(trans('profile.two_factor.disable'))
        ->assertSee($this->user->recoveryCodes()[0]);
});
