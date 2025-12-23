<?php

namespace App\Providers\Filament;

use App;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        if (! App::runningUnitTests()) {
            config([ 'livewire.inject_assets' => true ]);
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->spa(config('filament.spa'))
            ->sidebarCollapsibleOnDesktop(config('filament.sidebar_collapsible_on_desktop'))
            ->sidebarWidth(config('filament.sidebar_width'))
            ->viteTheme('resources/css/admin.css')
            ->favicon(file_exists($favIcon = storage_path('app/public/favicon.png')) ? asset('storage/favicon.png') . '?v=' . md5_file($favIcon) : null)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                               ->label(trans('nav.content')),
                NavigationGroup::make()
                               ->label(trans('nav.manage')),
                NavigationGroup::make()
                               ->label(trans('nav.external'))
                               ->collapsible(false)

            ])
            ->plugin(SpatieTranslatablePlugin::make()->defaultLocales([ 'en' ]))
            ->navigationItems([
                NavigationItem::make()
                              ->group(trans('nav.external'))
                              ->sort(101)
                              ->label(trans('nav.public-view'))
                              ->icon('heroicon-o-backward')
                              ->isActiveWhen(fn (): bool => false)
                              ->url('/'),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
