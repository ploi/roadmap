<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Settings\ColorSettings;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Card;
use Livewire\TemporaryUploadedFile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;

class Colors extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-color-swatch';

    protected static string $settings = ColorSettings::class;

    protected static ?string $navigationLabel = 'Theme';

    protected static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(UserRole::Admin);
    }

    public function mount(): void
    {
        parent::mount();

        abort_unless(auth()->user()->hasRole(UserRole::Admin), 403);
    }

    protected function getFormSchema(): array
    {
        return [
            Card::make([
                ColorPicker::make('primary'),
                FileUpload::make('favicon')
                    ->image()
                    ->helperText('Make sure your storage is linked (by running php artisan storage:link).')
                    ->disk('public')
                    ->imageResizeTargetHeight('64')
                    ->imageResizeTargetWidth('64')
                    ->maxSize(1024)
                    ->getUploadedFileUrlUsing(function ($record) {
                        return storage_path('app/public/favicon.png');
                    })
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        return (string)'favicon.png';
                    }),
            ])->columns(),
        ];
    }
}
