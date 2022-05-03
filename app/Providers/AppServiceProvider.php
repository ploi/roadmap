<?php

namespace App\Providers;

use Filament\Facades\Filament;
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
    }
}
