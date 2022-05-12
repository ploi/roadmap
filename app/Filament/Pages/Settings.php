<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;

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
                    ->placeholder('Enter defaults to be created upon project creation')
                    ->helperText('These boards will automatically be prefilled when you create a project.')
                    ->visible(fn($get) => $get('create_default_boards')),

                Toggle::make('show_projects_sidebar_without_boards')->label('Show projects in sidebar without boards')
                    ->helperText('If you don\'t want to show projects without boards in the sidebar, toggle this off.')
                    ->columnSpan(2),
                Toggle::make('allow_general_creation_of_item')->label('Allow general creation of an item')
                    ->helperText('This allows your users to create an item without a board.')
                    ->columnSpan(2),

                Repeater::make('dashboard_items')
                    ->columnSpan(2)
                    ->schema([
                        Select::make('type')->options([
                            'recent-items' => 'Recent items',
                            'recent-comments' => 'Recent comments'
                        ])->default('recent-items'),
                        Select::make('column_span')->options([
                            1 => 1,
                            2 => 2,
                        ])->default(1)
                    ])->helperText('Determine which items you want to show on the dashboard (for all users).'),


                RichEditor::make('welcome_text')
                    ->columnSpan(2)
                    ->helperText('This content will show at the top of the dashboard for (for all users).')
            ])->columns(),
        ];
    }
}
