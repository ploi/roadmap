<?php

namespace App\Providers;

use Filament\Facades\Filament;
use App\Services\OgImageGenerator;
use App\SocialProviders\SsoProvider;
use Illuminate\Support\Facades\View;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
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

        Filament::serving(static function () {
            Filament::registerTheme(mix('css/admin.css'));
        });

        Filament::registerNavigationItems([
            NavigationItem::make()
                          ->group('External')
                          ->sort(101)
                          ->label('Public view')
                          ->icon('heroicon-o-rewind')
                          ->isActiveWhen(fn (): bool => false)
                          ->url('/'),
        ]);

        if (file_exists($favIcon = storage_path('app/public/favicon.png'))) {
            config(['filament.favicon' => asset('storage/favicon.png') . '?v=' . md5_file($favIcon)]);
        }

        $this->bootSsoSocialite();
    }

    private function bootSsoSocialite(): void
    {
        $socialite = $this->app->make(SocialiteFactory::class);

        $socialite->extend('sso', static function ($app) use ($socialite) {
            $config = $app['config']['services.sso'];

            return $socialite->buildProvider(SsoProvider::class, $config);
        });
    }
}
