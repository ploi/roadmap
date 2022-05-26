<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\UserSocial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SocialProviders\SsoProvider;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider($provider = 'sso')
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request, $provider = 'sso')
    {
        if ($request->input('error') === 'access_denied') {
            return redirect()->route('login')->withErrors([
                'Denied access to login'
            ]);
        }

        $social = Socialite::driver($provider)->user();

        $userSocial = UserSocial::query()
            ->where('provider_id', $social->getId())
            ->where('provider', 'sso')
            ->first();

        // If we already have a social user, login using that.
        if ($userSocial) {
            $user = $userSocial->user;

            auth()->guard()->login($user, remember: true);

            return redirect()->intended($this->redirectPath());
        }

        $user = \App\Models\User::query()
            ->where('email', $social->getEmail())
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => strstr($social->getEmail(), '@', true),
                'email' => $social->getEmail(),
            ]);

            $user->userSocials()->create([
                'name' => strstr($social->getEmail(), '@', true),
                'provider' => 'sso',
                'provider_id' => $social->getId(),
                'access_token' => $social->token ? $social->token : null,
                'refresh_token' => $social->refreshToken ? $social->refreshToken : null
            ]);

            auth()->guard()->login($user, remember: true);

            return redirect()->intended($this->redirectPath());
        }

        if ($user && !$userSocial) {
            $user->userSocials()->create([
                'name' => strstr($social->getEmail(), '@', true),
                'provider' => 'sso',
                'provider_id' => $social->getId(),
                'access_token' => $social->token ? $social->token : null,
                'refresh_token' => $social->refreshToken ? $social->refreshToken : null
            ]);

            auth()->guard()->login($user, remember: true);

            return redirect()->intended($this->redirectPath());
        }

        return redirect()->route('home');
    }

    public function showLoginForm()
    {
        if (SsoProvider::isForced()) {
            return to_route('oauth.login');
        }

        return view('auth.login', [
            'hasSsoLoginAvailable' => SsoProvider::isEnabled(),
        ]);
    }
}
