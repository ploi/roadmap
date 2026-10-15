<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Log;
use Webauthn\Exception\WebauthnException;
use Laravel\Passkeys\Actions\VerifyPasskey;
use Illuminate\Validation\ValidationException;
use Laravel\Passkeys\Contracts\PasskeyLoginResponse;
use Laravel\Passkeys\Http\Requests\PasskeyVerificationRequest;
use Laravel\Passkeys\Http\Controllers\PasskeyLoginController as BasePasskeyLoginController;

/**
 * Passwordless sign in with a passkey, available once an admin enabled passkeys.
 *
 * laravel/passkeys only turns an unknown passkey into a validation error. A bad
 * signature, wrong origin or challenge, or a cloned authenticator surfaces as a
 * raw WebauthnException, which would otherwise render a 500. Those are logged,
 * because they can be an attack, and returned as a field error instead.
 */
class PasskeyLoginController extends BasePasskeyLoginController
{
    public function store(PasskeyVerificationRequest $request, VerifyPasskey $verify): PasskeyLoginResponse
    {
        try {
            return parent::store($request, $verify);
        } catch (WebauthnException $exception) {
            Log::warning('Passkey sign in rejected', [
                'exception' => $exception::class,
                'reason' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'credential' => [trans('auth.passkeys.verification_failed')],
            ]);
        }
    }
}
