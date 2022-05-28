<?php

namespace App\Providers;

use Filament\Facades\Filament;
use App\Services\OgImageGenerator;
use App\SocialProviders\SsoProvider;
use Illuminate\Support\Facades\View;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('partials.meta', function ($view) {
            $view->with(
                'defaultImage',
                (new OgImageGenerator())
                ->setSubject('Roadmap')
                ->setTitle(config('app.name'))
                ->setImageName('og.jpg')
                ->generateImage()
            );
        });

        Filament::serving(function () {
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

    private function bootSsoSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'sso',
            function ($app) use ($socialite) {
                $config = $app['config']['services.sso'];
                return $socialite->buildProvider(SsoProvider::class, $config);
            }
        );
    }
}
