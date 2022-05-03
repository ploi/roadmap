<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;

class Settings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = GeneralSettings::class;

    protected function getFormSchema(): array
    {
        return [
            Card::make([
                Toggle::make('board_centered')->label('Center boards in project views')
                    ->helperText('When centering, this will always show the boards in the center of the content area.')
                    ->columnSpan(2),

                Toggle::make('create_default_boards')->label('Create default boards for new projects')
                    ->helperText('When creating a new project, some default boards can be created.')
                    ->reactive(),

                TagsInput::make('default_boards')->label('Default boards')
                    ->visible(fn ($get) => $get('create_default_boards')),
            ])->columns(),
        ];
    }
}
