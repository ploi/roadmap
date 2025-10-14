<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class VerifyEmailChangeController extends Controller
{
    public function __invoke(Request $request, string $id, string $email): mixed
    {
        // Verify the signature of the URL
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired verification link.');
        }

        $user = User::findOrFail($id);

        // Verify that this is the authenticated user
        if (Auth::id() !== $user->id) {
            abort(403, 'You are not authorized to verify this email change.');
        }

        // Verify the email matches the pending email
        if ($user->pending_email !== $email) {
            abort(400, 'The verification link does not match the pending email change.');
        }

        // Update the email and clear pending email
        $user->update([
            'email' => $email,
            'pending_email' => null,
            'pending_email_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('profile')
            ->with('success', 'Your email address has been successfully updated!');
    }
}
