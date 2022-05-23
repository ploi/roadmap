<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Tailwind;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;

class PasswordProtectionController extends Controller
{
    public function __invoke()
    {
        $tw = new Tailwind('brand', app(\App\Settings\ColorSettings::class)->primary);

        return view('auth.password-protection', [
            'brandColors' => $tw->getCssFormat()
        ]);
    }

    public function login(Request $request)
    {
        if(app(GeneralSettings::class)->password !== $request->input('password')){
            return redirect()->back()->withErrors([
                'This is the wrong password.'
            ]);
        }

        $request->session()->put('password-login-authorized', true);

        return redirect()->route('home');
    }
}
