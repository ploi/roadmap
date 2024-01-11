<?php

namespace App\Filament\Resources\TagResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\TagResource;
use Filament\Resources\Pages\EditRecord;

class EditTag extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
