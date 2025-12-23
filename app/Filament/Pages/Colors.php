<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use Filament\Schemas\Schema;
use App\Settings\ColorSettings;
use Filament\Pages\SettingsPage;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Colors extends SettingsPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-swatch';

    protected static string $settings = ColorSettings::class;

    protected static ?int $navigationSort = 1400;

    public static function getNavigationGroup(): ?string
    {
        return trans('nav.manage');
    }

    public static function getNavigationLabel(): string
    {
        return trans('nav.theme');
    }

    public function getHeading(): string|Htmlable
    {
        return trans('theme.title');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(UserRole::Admin);
    }

    public function mount(): void
    {
        parent::mount();

        abort_unless(auth()->user()->hasRole(UserRole::Admin), 403);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(
            [
                Section::make()
                    ->columnSpanFull()
                    ->schema(
                        [
                            FileUpload::make('logo')
                                ->label(trans('theme.logo'))
                                ->image()
                                ->helperText(trans('theme.logo-helper-text'))
                                ->disk('public')
                                //                    ->imageResizeTargetHeight('64')
                                ->maxSize(1024)
                                ->getUploadedFileNameForStorageUsing(
                                    function (TemporaryUploadedFile $file): string {
                                        return (string)str($file->getClientOriginalName())->prepend('logo-');
                                    }
                                )
                                ->getUploadedFileNameForStorageUsing(
                                    function ($record) {
                                        return storage_path('app/public/' . app(ColorSettings::class)->logo);
                                    }
                                ),
                            FileUpload::make('favicon')
                                ->label(trans('theme.favicon'))
                                ->image()
                                ->disk('public')
                                //                    ->imageResizeTargetHeight('64')
                                //                    ->imageResizeTargetWidth('64')
                                ->maxSize(1024)
                                ->getUploadedFileNameForStorageUsing(
                                    function ($record) {
                                        return storage_path('app/public/favicon.png');
                                    }
                                )
                                ->getUploadedFileNameForStorageUsing(
                                    function (TemporaryUploadedFile $file): string {
                                        return (string)'favicon.png';
                                    }
                                ),
                            TextInput::make('fontFamily')
                                ->label(trans('theme.font-family'))
                                ->placeholder('e.g. Roboto')
                                ->required()
                                ->helperText(new HtmlString(trans('theme.font-family-helper-text'))),

                            ColorPicker::make('primary')
                                ->label(trans('theme.primary-color'))
                                ->default('#2563EB'),

                            Toggle::make('darkmode')
                                ->helperText('Allow darkmode on the frontend')
                        ]
                    )->columns(),
            ]
        );
    }
}
