<?php

use App\Settings\GeneralSettings;
use ParagonIE\ConstantTime\Base64UrlSafe;
use Laravel\Passkeys\Actions\VerifyPasskey;
use Laravel\Passkeys\Exceptions\InvalidPasskeyException;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Webauthn\Exception\AuthenticatorResponseVerificationException;

beforeEach(fn () => GeneralSettings::fake(['enable_passkeys' => true]));

/**
 * A structurally valid assertion; it is never verified because VerifyPasskey is mocked.
 *
 * @return array<string, mixed>
 */
function passkeyAssertionCredential(): array
{
    $rawId = Base64UrlSafe::encodeUnpadded('credential');

    return [
        'id' => $rawId,
        'rawId' => $rawId,
        'type' => 'public-key',
        'response' => [
            'clientDataJSON' => Base64UrlSafe::encodeUnpadded((string) json_encode([
                'type' => 'webauthn.get',
                'challenge' => Base64UrlSafe::encodeUnpadded('challenge'),
                'origin' => config('app.url'),
            ])),
            'authenticatorData' => Base64UrlSafe::encodeUnpadded(str_repeat("\0", 32)."\x05\0\0\0\x01"),
            'signature' => Base64UrlSafe::encodeUnpadded('signature'),
            'userHandle' => null,
        ],
        'clientExtensionResults' => [],
    ];
}

test('the login page offers passkey sign in when passkeys are enabled', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSeeText(trans('auth.passkeys.sign_in'))
        ->assertSee(route('passkey.login'));
});

test('the login page hides passkey sign in when passkeys are disabled', function () {
    GeneralSettings::fake(['enable_passkeys' => false]);

    $this->get(route('login'))
        ->assertOk()
        ->assertDontSeeText(trans('auth.passkeys.sign_in'));
});

test('the passkey sign in endpoints are hidden when passkeys are disabled', function () {
    GeneralSettings::fake(['enable_passkeys' => false]);

    $this->getJson(route('passkey.login-options'))->assertNotFound();
    $this->postJson(route('passkey.login'), ['credential' => passkeyAssertionCredential()])->assertNotFound();
});

test('sign in options ask for any discoverable passkey with user verification', function () {
    $response = $this->getJson(route('passkey.login-options'))->assertOk();

    expect($response->json('options.allowCredentials'))->toBeEmpty()
        ->and($response->json('options.userVerification'))->toBe('required')
        ->and(session('passkey.verification_options'))->not->toBeNull();
});

test('a verified passkey signs the user in without the two-factor challenge', function () {
    $user = createUser();
    app(EnableTwoFactorAuthentication::class)($user);
    $user->forceFill(['two_factor_confirmed_at' => now()])->save();
    $passkey = createPasskeyFor($user);

    $verify = Mockery::mock(VerifyPasskey::class);
    $verify->shouldReceive('__invoke')->once()->andReturn($passkey);
    $this->app->instance(VerifyPasskey::class, $verify);

    $this->getJson(route('passkey.login-options'))->assertOk();

    $this->postJson(route('passkey.login'), ['credential' => passkeyAssertionCredential(), 'remember' => true])
        ->assertOk()
        ->assertJson(['redirect' => url('/')]);

    $this->assertAuthenticatedAs($user);
});

test('an unrecognized passkey does not sign anyone in', function () {
    $verify = Mockery::mock(VerifyPasskey::class);
    $verify->shouldReceive('__invoke')->once()->andThrow(InvalidPasskeyException::make('Passkey not recognized.'));
    $this->app->instance(VerifyPasskey::class, $verify);

    $this->getJson(route('passkey.login-options'))->assertOk();

    $this->postJson(route('passkey.login'), ['credential' => passkeyAssertionCredential()])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('credential');

    $this->assertGuest();
});

test('a passkey failing the webauthn checks is a validation error instead of a server error', function () {
    $verify = Mockery::mock(VerifyPasskey::class);
    $verify->shouldReceive('__invoke')->once()->andThrow(AuthenticatorResponseVerificationException::create('Invalid origin.'));
    $this->app->instance(VerifyPasskey::class, $verify);

    $this->getJson(route('passkey.login-options'))->assertOk();

    $this->postJson(route('passkey.login'), ['credential' => passkeyAssertionCredential()])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['credential' => trans('auth.passkeys.verification_failed')]);

    $this->assertGuest();
});
