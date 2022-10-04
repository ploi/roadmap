<?php

namespace App\Filament\Resources\TagResource\Pages;

use Filament\Pages\Actions;
use App\Filament\Resources\TagResource;
use Filament\Resources\Pages\EditRecord;

class EditTag extends EditRecord
{
    protected static string $resource = TagResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
