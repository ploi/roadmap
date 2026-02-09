<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sso' => [
        'title' => env('SSO_LOGIN_TITLE', 'Login with SSO'),
        'url' => env('SSO_BASE_URL'),
        'client_id' => env('SSO_CLIENT_ID'),
        'client_secret' => env('SSO_CLIENT_SECRET'),
        'redirect' => env('SSO_CALLBACK'),
        'forced' => env('SSO_FORCED', false),
        'scopes' => env('SSO_SCOPES'),
        'http_verify' => env('SSO_HTTP_VERIFY', true),
        /**
         * Mostly your sso provider user endpoint response is wrapped in a `data` key.
         * for example: { "data": "id": "name": "John Doe", "email": "john@example.com" }
         * If you would like to use a custom key instead of data, you may define it here.
         * you can also set something like 'data.user' if its nested.
         * or you can set it to nothing (do not set it to value 'null'. just leave it empty value)
         * if sso provider user endpoint response is not wrapped in a key.
         */
        'provider_user_endpoint_data_wrap_key' => env('SSO_PROVIDER_USER_ENDPOINT_DATA_WRAP_KEY'),
        // The keys that should be present in the sso provider user endpoint response
        'provider_user_endpoint_keys' => env('SSO_PROVIDER_USER_ENDPOINT_KEYS', 'id,email,name'),
        // The provider id returned by the sso provider for the user identification. sometimes its `uuid` instead of `id`
        'provider_id' => env('SSO_PROVIDER_ID', 'id'),
        'endpoints' => [
            'authorize' => env('SSO_ENDPOINT_AUTHORIZE'),
            'revoke' => env('SSO_ENDPOINT_REVOKE'),
            'token' => env('SSO_ENDPOINT_TOKEN'),
            'user' => env('SSO_ENDPOINT_USER'),
        ],
    ],

    'gravatar' => [
        'base_url' => rtrim(env('GRAVATAR_BASE_URL', 'https://www.gravatar.com/avatar'), '/'),
    ],

    'fathom' => [
        'site_id' => env('FATHOM_SITE_ID'),
    ],
];
