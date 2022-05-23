# Roadmap

Welcome to Roadmap, the open-source software for your roadmapping needs 🛣

## Features

- Completely customisable roadmapping software
- Mention users in comments
- Upvote items to see which has more priority
- Filament admin panel 💛

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
```

Now edit your `.env` file and set up the database credentials, including the app name you want.

Now run the following:

```
php artisan migrate --force
php artisan make:filament-user
```

And login with the credentials you've provided.

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

## Credits

- [Cannonb4ll](https://github.com/cannonb4ll)
- [SebastiaanKloos](https://github.com/SebastiaanKloos)
- [Filament Admin](https://filamentadmin.com/)
- [Laravel](https://laravel.com/)
- [Razor UI](https://razorui.com/)
- [Ploi](https://ploi.io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
