<?php

use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Profile;
use Laravel\Passkeys\Passkey;
use App\Settings\GeneralSettings;
use function Pest\Laravel\actingAs;
use ParagonIE\ConstantTime\Base64UrlSafe;
use Laravel\Passkeys\Actions\StorePasskey;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Webauthn\Exception\AuthenticatorResponseVerificationException;

beforeEach(function () {
    GeneralSettings::fake(['enable_passkeys' => true]);

    $this->user = User::factory()->create();
    actingAs($this->user);
});

/**
 * A structurally valid "none" attestation; it is never verified because StorePasskey is mocked.
 *
 * @return array<string, mixed>
 */
function passkeyAttestationCredential(): array
{
    $authenticatorData = str_repeat("\0", 32)."\x05\0\0\0\0";
    // CBOR map {"fmt": "none", "attStmt": {}, "authData": <37 bytes>}.
    $attestationObject = "\xA3"
        ."\x63fmt"."\x64none"
        ."\x67attStmt"."\xA0"
        ."\x68authData"."\x58".chr(strlen($authenticatorData)).$authenticatorData;

    $rawId = Base64UrlSafe::encodeUnpadded('new-credential');

    return [
        'id' => $rawId,
        'rawId' => $rawId,
        'type' => 'public-key',
        'response' => [
            'clientDataJSON' => Base64UrlSafe::encodeUnpadded((string) json_encode([
                'type' => 'webauthn.create',
                'challenge' => Base64UrlSafe::encodeUnpadded('challenge'),
                'origin' => config('app.url'),
            ])),
            'attestationObject' => Base64UrlSafe::encodeUnpadded($attestationObject),
            'transports' => ['internal'],
        ],
        'clientExtensionResults' => [],
    ];
}

test('the security tab lists the users passkeys', function () {
    createPasskeyFor($this->user, name: 'Work YubiKey');

    Livewire::test(Profile::class)
        ->assertSeeText(trans('profile.passkeys.heading'))
        ->assertSeeText('Work YubiKey');
});

test('passkeys are hidden from the profile when passkeys are disabled', function () {
    GeneralSettings::fake(['enable_passkeys' => false]);

    Livewire::test(Profile::class)
        ->assertDontSeeText(trans('profile.passkeys.heading'))
        ->assertActionHidden('addPasskey');
});

test('adding a passkey requires the correct password', function () {
    Livewire::test(Profile::class)
        ->callAction('addPasskey', ['name' => 'MacBook Touch ID', 'current_password' => 'wrong-password'])
        ->assertHasActionErrors(['current_password'])
        ->assertNotDispatched('passkey-registration-started');

    expect(session('passkey.registration'))->toBeNull();
});

test('adding a passkey issues creation options for the user', function () {
    Livewire::test(Profile::class)
        ->callAction('addPasskey', ['name' => 'MacBook Touch ID', 'current_password' => 'password'])
        ->assertHasNoActionErrors()
        ->assertDispatched(
            'passkey-registration-started',
            fn (string $event, array $params): bool => $params['options']['user']['name'] === $this->user->email
                && $params['options']['authenticatorSelection']['userVerification'] === 'required',
        );

    expect(session('passkey.registration.name'))->toBe('MacBook Touch ID');
});

test('finishing a registration stores the verified passkey under the chosen name', function () {
    $store = Mockery::mock(StorePasskey::class);
    $store->shouldReceive('__invoke')
        ->once()
        ->withArgs(fn (User $owner, string $name): bool => $owner->is($this->user) && $name === 'MacBook Touch ID')
        ->andReturnUsing(fn (): Passkey => createPasskeyFor($this->user, 'new-credential'));
    $this->app->instance(StorePasskey::class, $store);

    Livewire::test(Profile::class)
        ->callAction('addPasskey', ['name' => 'MacBook Touch ID', 'current_password' => 'password'])
        ->call('finishPasskeyRegistration', passkeyAttestationCredential())
        ->assertNotified(trans('profile.passkeys.added_notification'));

    expect(session('passkey.registration'))->toBeNull();
});

test('finishing a registration without confirming the password first is rejected', function () {
    $store = Mockery::mock(StorePasskey::class);
    $store->shouldNotReceive('__invoke');
    $this->app->instance(StorePasskey::class, $store);

    Livewire::test(Profile::class)
        ->call('finishPasskeyRegistration', passkeyAttestationCredential())
        ->assertNotified(trans('profile.passkeys.registration_expired'));
});

test('a passkey failing the webauthn checks is not stored', function () {
    $store = Mockery::mock(StorePasskey::class);
    $store->shouldReceive('__invoke')->once()->andThrow(AuthenticatorResponseVerificationException::create('Invalid origin.'));
    $this->app->instance(StorePasskey::class, $store);

    Livewire::test(Profile::class)
        ->callAction('addPasskey', ['name' => 'MacBook Touch ID', 'current_password' => 'password'])
        ->call('finishPasskeyRegistration', passkeyAttestationCredential())
        ->assertNotified(trans('profile.passkeys.registration_failed'));

    expect(Passkey::query()->count())->toBe(0);
});

test('a user can remove their own passkey with the correct password', function () {
    $passkey = createPasskeyFor($this->user);

    Livewire::test(Profile::class)
        ->callAction('deletePasskey', ['current_password' => 'password'], ['passkey' => $passkey->id])
        ->assertHasNoActionErrors();

    $this->assertModelMissing($passkey);
});

test('removing a passkey requires the correct password', function () {
    $passkey = createPasskeyFor($this->user);

    Livewire::test(Profile::class)
        ->callAction('deletePasskey', ['current_password' => 'wrong-password'], ['passkey' => $passkey->id])
        ->assertHasActionErrors(['current_password']);

    $this->assertModelExists($passkey);
});

test('a user cannot remove someone elses passkey', function () {
    $passkey = createPasskeyFor(User::factory()->create());

    expect(
        fn () => Livewire::test(Profile::class)
        ->callAction('deletePasskey', ['current_password' => 'password'], ['passkey' => $passkey->id])
    )->toThrow(ModelNotFoundException::class);

    $this->assertModelExists($passkey);
});
