![Alt text](/public/screenshots/screenshot.png)

# Roadmap

Welcome to Roadmap, the open-source software for your roadmapping needs 🛣

## Features

- Completely customisable roadmapping software
- Mention users in comments
- Upvote items to see which has more priority
- Automatic slug generation
- Filament admin panel 💛
- Automatic OG image generation including branding color you've setup (saves in your storage, around 70kb per image), if title is too long it will strip automatically as well, example:
![OG](https://roadmap.ploi.io/storage/og-20-ssl-via-api-force-request-skip-dns-verification-site-level-request-20.jpg?v=1653397308)

## Requirements

- PHP >= 8.1
- Database (MySQL, PostgreSQL)

## Installation

First set up a database, and remember the credentials.

```
git clone https://github.com/ploi-deploy/roadmap.git
composer install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
npm install
npm run production
```

Now edit your `.env` file and set up the database credentials, including the app name you want.

Now run the following:

```
php artisan migrate --force
php artisan make:filament-user
```

And login with the credentials you've provided.

## Installing SSO (oAuth login with 3rd party app)

It is possible to configure oAuth login with this roadmap software to make it easier to log in.
In this example we're going to show how to set this up with a Laravel application example, but any other oAuth should be able to integrate as well.

Start by installing Laravel Passport into your application, [consult their docs how to do this](https://laravel.com/docs/9.x/passport#installation).

Now create a fresh client by running `php artisan passport:client`

It will ask you a few questions, an example how to answer these:

```
$ php artisan passport:client

Which user ID should the client be assigned to?:
> 

What should we name the client?:
> Roadmap SSO

Where should we redirect the request after authorization? [https://my-app.com/oauth/callback]:
> 

New client created successfully.
Client ID: 3
Client secret: 9Mqb2ssCDwk0BBiRwyRZPVupzkdphgfuBgEsgpjQ
```

Enter these credentials inside your .env file of the roadmap:

```
SSO_LOGIN_TITLE="Login with SSO"
SSO_BASE_URL=https://external-app.com
SSO_CLIENT_ID=3
SSO_CLIENT_SECRET=9Mqb2ssCDwk0BBiRwyRZPVupzkdphgfuBgEsgpjQ
SSO_CALLBACK=${APP_URL}/oauth/callback
```

Next we're going to prepare the routes, controller & resource for your application.

Create these routes inside the `api.php` file:

```php
Route::get('oauth/user', 'UserOAuthController@user')->middleware('scopes:email');
Route::delete('oauth/revoke', 'UserOAuthController@revoke');
```

Create the resource: `php artisan make:resource Api\UserOAuthResource` with the following contents in the `toArray()` method:

```php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
    ];
}
```

Create a controller `php artisan make:controller Api\UserOAuthController` and add these functions:

```php
use App\Http\Resources\Api\UserOAuthResource;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;

public function user(Request $request)
{
    return new UserOAuthResource($request->user());
}

public function revoke(Request $request)
{
    $token = $request->user()->token();

    $tokenRepository = app(TokenRepository::class);
    $refreshTokenRepository = app(RefreshTokenRepository::class);

    $tokenRepository->revokeAccessToken($token->id);

    $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
}
```

Also setup the tokens inside the `AppServiceProvider` inside the `boot()` method:

```php
public function boot()
{
    ... 
    
    Passport::tokensCan([
        'email' => 'Read email'
    ]);
}
```

Now head over to the login page in your roadmap software and view the log in button in action. The title of the button can be set with the `.env` variable: `SSO_LOGIN_TITLE=`

## Testing

```bash
./vendor/bin/pest
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Sponsor

We appreciate sponsors, we still maintain this repository, server, emails and domain. [You can do that here](https://github.com/sponsors/Cannonb4ll).
Each sponsor gets listed on in this readme.

## Paid alternatives

Obviously, if you do not want to self host, there's plenty of self-hosted solutions, a small rundown:

- [UpVoty](https://upvoty.com)
- [Canny](https://canny.io/)
- [Craft](https://craft.io/)
- [Convas](https://convas.io/)
- [UserReport](https://www.userreport.com/)

## Credits

- [Cannonb4ll](https://github.com/cannonb4ll)
- [SebastiaanKloos](https://github.com/SebastiaanKloos)
- [Filament Admin](https://filamentadmin.com/)
- [Laravel](https://laravel.com/)
- [Razor UI](https://razorui.com/)
- [Ploi](https://ploi.io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
