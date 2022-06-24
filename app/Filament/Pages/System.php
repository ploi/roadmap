<?php

namespace App\Filament\Pages;

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
}
