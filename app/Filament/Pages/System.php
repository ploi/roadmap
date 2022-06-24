<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Widgets\System\SystemInfo;
use Filament\Pages\Page;

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
}
