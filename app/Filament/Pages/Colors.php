<?php

namespace App\Filament\Pages;

use App\Settings\ColorSettings;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\ColorPicker;

class Colors extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-color-swatch';

    protected static string $settings = ColorSettings::class;

    protected function getFormSchema(): array
    {
        return [
            Card::make([
                ColorPicker::make('primary')
            ])->columns(),
        ];
    }
}
