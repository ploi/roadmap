<?php

namespace App\SocialProviders;

use RuntimeException;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\User;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;

class SsoProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopes = [
        'email',
    ];
    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new Client(array_merge($this->guzzle, [
                'verify' => config('services.sso.http_verify', true),
            ]));
        }

        return $this->httpClient;
    }

    protected function getAuthUrl($state)
    {
        $endpoint = config('services.sso.endpoints.authorize') ?? config('services.sso.url') . '/oauth/authorize';

        return $this->buildAuthUrlFromBase($endpoint, $state);
    }

    protected function getTokenUrl()
    {
        return config('services.sso.endpoints.token') ?? config('services.sso.url') . '/oauth/token';
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
        $endpoint = config('services.sso.endpoints.user') ?? config('services.sso.url') . '/api/oauth/user';

        $response = $this->getHttpClient()->get($endpoint, [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);


        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        $user = Arr::get($user, 'data');

        if ($user === null || !Arr::has($user, ['id', 'email', 'name'])) {
            throw new RuntimeException('The SSO user endpoint should return an `id`, `email` and `name` wrapped in the `data` field.');
        }

        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'nickname' => $user['name'],
        ]);
    }
}
