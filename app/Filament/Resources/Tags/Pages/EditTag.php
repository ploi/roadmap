<?php

namespace App\Filament\Resources\Tags\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Tags\TagResource;
use Filament\Resources\Pages\EditRecord;

class EditTag extends EditRecord
{
    use Translatable;

    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
