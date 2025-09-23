<?php

namespace App\Providers;

use App\Http\Kernel;
use App\Services\OgImageGenerator;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\Collection;
use App\SocialProviders\SsoProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class AppServiceProvider extends ServiceProvider
{
    public function boot(Kernel $kernel): void
    {
        View::composer('partials.meta', static function ($view) {
            $view->with(
                'defaultImage',
                OgImageGenerator::make(config('app.name'))
                    ->withSubject('Roadmap')
                    ->withPolygonDecoration()
                    ->withFilename('og.jpg')
                    ->generate()
                    ->getPublicUrl()
            );
        });

        $this->bootSsoSocialite();
        $this->bootCollectionMacros();

        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => Color::Blue,
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]);
    }

    private function bootSsoSocialite(): void
    {
        $socialite = $this->app->make(SocialiteFactory::class);

        $socialite->extend('sso', static function ($app) use ($socialite) {
            $config = $app['config']['services.sso'];

            return $socialite->buildProvider(SsoProvider::class, $config);
        });
    }

    private function bootCollectionMacros(): void
    {
        Collection::macro('prioritize', function ($callback): Collection {
            $nonPrioritized = $this->reject($callback);

            return $this
                ->filter($callback)
                ->merge($nonPrioritized);
        });
    }
}
