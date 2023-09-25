<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use App\Services\SystemChecker;
use App\Filament\Pages\Widgets\System\SystemInfo;
use Filament\Support\Enums\Alignment;

class System extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string $view = 'filament.pages.system';

    protected static ?int $navigationSort = 0;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(UserRole::Admin);
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole(UserRole::Admin), 403);
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
            return 'Update available';
        }

        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('check_for_updates')
                ->color('gray')
                ->action(function () {
                    (new SystemChecker())->flushVersionData();

                    Notification::make('check_for_updates')
                        ->title('Updates')
                        ->body('Version data has been updated')
                        ->success()
                        ->send();

                    return redirect(System::getUrl());
                })
                ->requiresConfirmation()
                ->modalAlignment(Alignment::Left),
        ];
    }
}
