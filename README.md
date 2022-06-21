![Roadmap screenshot](/public/screenshots/screenshot.png)

# Roadmap

Welcome to Roadmap, the open-source software for your roadmapping needs 🛣

## Features

- Completely customisable roadmapping software
- Mention users in comments
- Upvote items to see which has more priority
- Automatic slug generation
- Filament admin panel 💛
- Simplified role system (administrator, employee & user)
- OAuth 2 single sign-on with your own application
- Automatic OG image generation including branding color you've setup (saves in your storage, around 70kb per image), if title is too long it will strip automatically as well, example:

![OG](https://roadmap.ploi.io/storage/og-20-ssl-via-api-force-request-skip-dns-verification-site-level-request-20.jpg?v=1653765303)

## Requirements

- PHP >= 8.1
- Database (MySQL, PostgreSQL)
- GD Library (>=2.0) or
- Imagick PHP extension (>=6.5.7)

## Installation

First set up a database, and remember the credentials.

```
git clone https://github.com/ploi-deploy/roadmap.git
composer install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
```

Now edit your `.env` file and set up the database credentials, including the app name you want.

Optionally you may set up the language with `APP_LOCALE`, if your language is not working we accept PR's for new languages. We recommend copying those files from the `lang/en` folder.
As well as the timezone can be set with `APP_TIMEZONE`, for example: `APP_TIMEZONE="Europe/Amsterdam"`.

Now run the following:

```
php artisan roadmap:install
```

And login with the credentials you've provided, the user you've created will automatically be admin.

## Deployment

To manage your servers and sites, we recommend using [Ploi.io](https://ploi.io/?ref=roadmap-readme) to speed up things, obviously you're free to choose however you'd like to deploy this piece of software 💙

That being said, here's an deployment script example:

```sh
cd /home/ploi/example.com
git pull origin main
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
echo "" | sudo -S service php8.1-fpm reload

php artisan route:cache
php artisan view:clear
php artisan migrate --force

npm ci
npm run production

echo "🚀 Application deployed!"
```

If you're using queue workers (which we recommend to do) also add `php artisan queue:restart` to your deployment script.

## Role system

There's a simplified role system included in this roadmapping software. There's 3 roles: administrator, employee & user.

What are these roles allowed to do?

- Administrator
  - Obviously anything to users, items, projects, access admin
- Employee
  - These can access the admin, and see their assigned items (via a filter). What they can't do: settings, theme, users, CRUD projects.
- User
  - This is your default user when someone registers, they don't have access to the administration and can only access the frontend.

## Installing SSO (OAuth 2 login with 3rd party app)

It is possible to configure OAuth 2 login with this roadmap software to make it easier to log in.
In this example we're going to show how to set this up with a Laravel application example, but any other OAuth 2 capable application should be able to integrate as well.

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
Route::get('oauth/user', [Api\UserOAuthController::class, 'user'])->middleware('scopes:email');
Route::delete('oauth/revoke', [Api\UserOAuthController::class, 'revoke']);
```

Create the resource: `php artisan make:resource Api/UserOAuthResource` with the following contents in the `toArray()` method:

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

Create a controller `php artisan make:controller Api/UserOAuthController` and add these functions:

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


## Docker Support

### Getting up and running...

Go into docker folder and run:
`docker-compose up -d --build`

Set your database .env variables:
```
DB_CONNECTION=mysql
DB_HOST=roadmap-db
DB_PORT=3306
DB_DATABASE=roadmap
DB_USERNAME=root
DB_PASSWORD=secret
```

Composer Install:

`docker exec -it roadmap composer install`

NPM Install:

`docker exec -it roadmap npm ci`

Running artisan commands:

`docker exec -it roadmap php artisan <command>`

The Application will be running on `localhost:1337` and PhpMyAdmin is running on `localhost:8010`

### Docker Considerations

There are a few heroicons that were giving issues when running locally with docker.

```
Unable to locate a class or view for component <insert heroicon name here>
```
The problem was resolved by simply changing the following icons:

x-heroicon-o-chevron-down -> x-heroicon-s-chevron-down (group.blade.php)
heroicon-o-chat -> heroicon-s-chat (CommentResource)
heroicon-o-archive -> heroicon-s-archive (ItemResource)
heroicon-o-x-circle -> heroicon-o-collection (notifications.blade.php)

Related Issues:

[laravel-filament/filament#2677](https://github.com/laravel-filament/filament/issues/2677)

[blade-ui-kit/blade-heroicons#9](https://github.com/blade-ui-kit/blade-heroicons/issues/9)

## Testing

```bash
composer test
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
