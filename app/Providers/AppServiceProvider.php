<?php

namespace App\Providers;

use App\Http\Kernel;
use Filament\Facades\Filament;
use Illuminate\Foundation\Vite;
use App\Settings\GeneralSettings;
use App\Services\OgImageGenerator;
use Illuminate\Support\Collection;
use App\SocialProviders\SsoProvider;
use Illuminate\Support\Facades\View;
use Filament\Navigation\NavigationItem;
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

        Filament::serving(function () {
            Filament::registerRenderHook(
                'head.end',
                static fn () => (new Vite)(['resources/css/admin.css'])
            );
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
        $this->bootCollectionMacros();

//        if (app(GeneralSettings::class)->users_must_verify_email) {
//            $this->addVerificationMiddleware($kernel);
//        }
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

    protected function addVerificationMiddleware(Kernel $kernel)
    {
        $kernel->appendMiddlewareToGroup('authed', 'verified');
    }
}
