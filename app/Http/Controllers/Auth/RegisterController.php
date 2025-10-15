<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Settings\GeneralSettings;
use App\Http\Controllers\Controller;
use App\SocialProviders\SsoProvider;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Spatie\Honeypot\ProtectAgainstSpam;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    use RegistersUsers {
        showRegistrationForm as private laravelShowRegistrationForm;
    }

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware(ProtectAgainstSpam::class)->only('register');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required', 'string', 'confirmed',
                Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols(),
            ],
        ]);
    }

    protected function create(array $data)
    {
        if (app(GeneralSettings::class)->disable_user_registration) {
            abort(301, 'User registration is disabled.');
        }

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function showRegistrationForm()
    {
        if (app(GeneralSettings::class)->disable_user_registration) {
            return redirect()->route('home');
        }

        if (SsoProvider::isForced()) {
            return to_route('oauth.login');
        }

        return $this->laravelShowRegistrationForm();
    }
}
