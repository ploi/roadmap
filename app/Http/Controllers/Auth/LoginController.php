<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\UserSocial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SocialProviders\SsoProvider;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
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
            ->whereNotNull('provider_id')
            ->first();

        // If we already have a social user, login using that.
        if ($userSocial) {
            $user = $userSocial->user;

            $user->markEmailAsVerified();

            auth()->guard()->login($user, remember: true);

            return redirect()->intended($this->redirectPath());
        }

        $user = User::query()
            ->where('email', $social->getEmail())
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $social->getName(),
                'email' => $social->getEmail(),
            ]);

            $user->markEmailAsVerified();

            $user->userSocials()->create([
                'name' => $social->getName(),
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
                'name' => $social->getName(),
                'provider' => 'sso',
                'provider_id' => $social->getId(),
                'access_token' => $social->token ? $social->token : null,
                'refresh_token' => $social->refreshToken ? $social->refreshToken : null
            ]);

            $user->markEmailAsVerified();

            auth()->guard()->login($user, remember: true);

            return redirect()->intended($this->redirectPath());
        }

        return redirect()->route('home');
    }

    public function showLoginForm(Request $request)
    {
        if ($request->has('intended')) {
            Session::put('url.intended', $request->input('intended'));
        }

        if (SsoProvider::isForced()) {
            return to_route('oauth.login');
        }

        return view('auth.login', [
            'hasSsoLoginAvailable' => SsoProvider::isEnabled(),
        ]);
    }
}
