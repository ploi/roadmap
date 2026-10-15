<?php

use App\Models\User;
use Laravel\Passkeys\Passkey;
use Tests\CreatesApplication;
use function Pest\Laravel\actingAs;
use Illuminate\Support\Facades\Mail;
use ParagonIE\ConstantTime\Base64UrlSafe;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(
    TestCase::class,
    CreatesApplication::class,
    RefreshDatabase::class
)
    ->beforeEach(fn () => Mail::fake())
    ->in('Feature', 'Unit');

function createUser($attributes = [], $has = [])
{
    $user = User::factory();

    if ($has) {
        foreach ($has as $relationship => $attached) {
            $user = $user->has($attached, is_string($relationship) ? $relationship : null);
        }
    }

    return $user->create($attributes);
}

function createAndLoginUser($attributes = [], ?User $user = null)
{
    if (!$user) {
        $user = createUser($attributes);
    }

    actingAs($user);

    return $user;
}

/**
 * Store a passkey for the user without running a WebAuthn ceremony.
 */
function createPasskeyFor(User $user, string $rawCredentialId = 'credential', string $name = 'MacBook Touch ID'): Passkey
{
    $passkey = new Passkey;
    $passkey->forceFill([
        'user_id' => $user->id,
        'name' => $name,
        'credential_id' => Base64UrlSafe::encodeUnpadded($rawCredentialId),
        'credential' => [],
    ])->save();

    return $passkey;
}
