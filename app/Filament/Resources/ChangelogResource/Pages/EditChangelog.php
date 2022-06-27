<?php

namespace App\Filament\Resources\ChangelogResource\Pages;

use App\Filament\Resources\ChangelogResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChangelog extends EditRecord
{
    protected static string $resource = ChangelogResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
