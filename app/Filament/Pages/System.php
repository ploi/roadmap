<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Services\SystemChecker;
use Filament\Support\Enums\Alignment;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Pages\Widgets\System\SystemInfo;

class System extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string $view = 'filament.pages.system';

    protected static ?int $navigationSort = 1500;

    public static function getNavigationGroup(): ?string
    {
        return trans('nav.manage');
    }

    public static function getNavigationLabel(): string
    {
        return trans('nav.system');
    }

    public function getHeading(): string|Htmlable
    {
        return trans('system.title');
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->hasRole(UserRole::Admin);
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        abort_unless($user->hasRole(UserRole::Admin), 403);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SystemInfo::class
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $systemChecker = new SystemChecker();

        if ($systemChecker->isOutOfDate()) {
            return trans('system.update-available');
        }

        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('check_for_updates')
                ->label(trans('system.check-for-updates'))
                ->color('gray')
                ->action(
                    function () {
                        (new SystemChecker())->flushVersionData();

                        Notification::make('check_for_updates')
                            ->title(trans('system.updates'))
                            ->body(trans('system.version-updated'))
                            ->success()
                            ->send();

                        return redirect(System::getUrl());
                    }
                )
                ->requiresConfirmation()
                ->modalAlignment(Alignment::Left),
        ];
    }
}
