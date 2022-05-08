<?php

namespace App\Filament\Pages;

use App\Settings\ColorSettings;
use Filament\Forms\Components\ColorPicker;
use Filament\Pages\SettingsPage;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;

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
