<?php

namespace App\SocialProviders;

use RuntimeException;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\User;
use App\Exceptions\SsoException;
use GuzzleHttp\Exception\ClientException;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;

class SsoProvider extends AbstractProvider implements ProviderInterface
{
    public function getScopes()
    {
        if (config('services.sso.scopes') !== null) {
            return explode(',', config('services.sso.scopes'));
        }

        return ['email'];
    }

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

        try {
            $response = $this->getHttpClient()->get($endpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);
        } catch (ClientException $exception) {
            $json = $exception->getResponse()->getBody()->getContents();

            throw new SsoException($json);
        }

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        $providerUserEndpointDataWrapKey = config('services.sso.provider_user_endpoint_data_wrap_key');
        $providerUserEndpointKeys = config('services.sso.provider_user_endpoint_keys') ?? 'id,email,name';
        $providerId = config('services.sso.provider_id') ?? 'id';

        if($providerUserEndpointDataWrapKey !== null) {
            $user = Arr::get($user, $providerUserEndpointDataWrapKey);
        }
        
        if ($user === null || !empty(array_intersect($user, explode(',', $providerUserEndpointKeys)))) {
            if($providerUserEndpointDataWrapKey !== null) {
              throw new RuntimeException("The SSO user endpoint should return an {$providerUserEndpointKeys} in the `{$providerUserEndpointDataWrapKey}` field of the JSON response.");
            } else {
              throw new RuntimeException("The SSO user endpoint should return an {$providerUserEndpointKeys} in the JSON response.");
            }
        }

        return (new User)->setRaw($user)->map([
            'id' => Arr::get($user, $providerId),
            'email' => $user['email'],
            'name' => $user['name'],
            'nickname' => $user['name'],
        ]);
    }

    public static function isEnabled(): bool
    {
        return config('services.sso.url') &&
            config('services.sso.client_id') &&
            config('services.sso.client_secret') &&
            config('services.sso.redirect');
    }

    public static function isForced(): bool
    {
        return self::isEnabled() && config('services.sso.forced') === true;
    }
}
