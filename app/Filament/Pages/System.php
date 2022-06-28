<?php

namespace App\Filament\Pages;

use App\Services\SystemChecker;
use Filament\Pages\Page;
use App\Filament\Pages\Widgets\System\SystemInfo;

class System extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chip';

    protected static string $view = 'filament.pages.system';

    protected static ?int $navigationSort = 0;

    protected function getHeaderWidgets(): array
    {
        return [
            SystemInfo::class
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        $systemChecker = new SystemChecker();

        if ($systemChecker->isOutOfDate()) {
            return 'Update available';
        }

        return null;
    }
}
