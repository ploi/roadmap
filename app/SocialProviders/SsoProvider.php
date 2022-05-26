<?php

namespace App\SocialProviders;

use Illuminate\Support\Arr;
use Laravel\Socialite\Two\User;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;

class SsoProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopes = [
        'email',
    ];

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(config('services.sso.url') . '/oauth/authorize', $state);
    }

    protected function getTokenUrl()
    {
        return config('services.sso.url') . '/oauth/token';
    }

    protected function getTokenFields($code)
    {
        return Arr::add(
            parent::getTokenFields($code),
            'grant_type',
            'authorization_code'
        );
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(config('services.sso.url') . '/api/oauth/user', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function formatScopes(array $scopes, $scopeSeparator)
    {
        return implode($scopeSeparator, $scopes);
    }

    protected function mapUserToObject(array $user)
    {
        $user = Arr::get($user, 'data');

        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'nickname' => $user['name'],
        ]);
    }
}
