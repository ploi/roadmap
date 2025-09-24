<?php

namespace App\Http\Controllers\Auth;

use App\Settings\ColorSettings;
use App\Services\Tailwind;
use Illuminate\Http\Request;
use App\Settings\GeneralSettings;
use App\Http\Controllers\Controller;

class PasswordProtectionController extends Controller
{
    public function __invoke()
    {
        $tw = new Tailwind('brand', app(ColorSettings::class)->primary);

        return view('auth.password-protection', [
            'brandColors' => $tw->getCssFormat()
        ]);
    }

    public function login(Request $request)
    {
        if (app(GeneralSettings::class)->password !== $request->input('password')) {
            return redirect()->back()->withErrors([
                'This is the wrong password.'
            ]);
        }

        $request->session()->put('password-login-authorized', true);

        return redirect()->route('home');
    }
}
