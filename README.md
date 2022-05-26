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
