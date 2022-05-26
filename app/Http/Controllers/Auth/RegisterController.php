<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\SocialProviders\SsoProvider;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Spatie\Honeypot\ProtectAgainstSpam;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function showRegistrationForm()
    {
        if (SsoProvider::isForced()) {
            return to_route('oauth.login');
        }

        return $this->laravelShowRegistrationForm();
    }
}
