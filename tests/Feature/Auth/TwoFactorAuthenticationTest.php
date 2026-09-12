<?php

use App\Models\User;
use Laravel\Fortify\Fortify;
use PragmaRX\Google2FA\Google2FA;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;

/**
 * Enable and confirm two-factor authentication for the given user,
 * returning the plaintext TOTP secret so tests can generate valid codes.
 *
 * Confirmation is marked directly rather than by verifying a live code, so the
 * current window's code is not consumed by Fortify's replay protection (which
 * would then reject the same code when the challenge tests reuse it).
 */
function enableTwoFactorFor(User $user): string
{
    app(EnableTwoFactorAuthentication::class)($user);

    $user->forceFill(['two_factor_confirmed_at' => now()])->save();
    $user->refresh();

    return Fortify::currentEncrypter()->decrypt($user->two_factor_secret);
}

test('a user without two-factor authentication logs in directly', function () {
    $user = createUser();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});

test('a user with two-factor enabled is redirected to the challenge instead of being logged in', function () {
    $user = createUser();
    enableTwoFactorFor($user);

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
    expect(session('login.id'))->toBe($user->id);
});

test('the two-factor challenge screen renders for a challenged user', function () {
    $user = createUser();
    enableTwoFactorFor($user);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->get(route('two-factor.login'))
        ->assertOk()
        ->assertViewIs('auth.two-factor-challenge');
});

test('the two-factor challenge redirects to login without a challenged user', function () {
    $this->get(route('two-factor.login'))->assertRedirect(route('login'));
});

test('a valid authenticator code completes the login', function () {
    $user = createUser();
    $secret = enableTwoFactorFor($user);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->post(route('two-factor.login.store'), [
        'code' => (new Google2FA)->getCurrentOtp($secret),
    ])->assertRedirect(config('fortify.home'));

    $this->assertAuthenticatedAs($user);
});

test('an invalid authenticator code is rejected', function () {
    $user = createUser();
    enableTwoFactorFor($user);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->from(route('two-factor.login'))
        ->post(route('two-factor.login.store'), ['code' => '000000'])
        ->assertRedirect(route('two-factor.login'))
        ->assertSessionHasErrors();

    $this->assertGuest();
});

test('a recovery code completes the login', function () {
    $user = createUser();
    enableTwoFactorFor($user);
    $user->refresh();

    $recoveryCode = $user->recoveryCodes()[0];

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->post(route('two-factor.login.store'), [
        'recovery_code' => $recoveryCode,
    ])->assertRedirect(config('fortify.home'));

    $this->assertAuthenticatedAs($user);

    // The used recovery code should be rotated out.
    expect($user->fresh()->recoveryCodes())->not->toContain($recoveryCode);
});

test('an incorrect password does not start a two-factor challenge', function () {
    $user = createUser();
    enableTwoFactorFor($user);

    $this->from(route('login'))->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertRedirect(route('login'));

    $this->assertGuest();
    expect(session('login.id'))->toBeNull();
});
