<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    public function getActions(): array
    {
        return [
            Action::make('view_public')->color('secondary')->url(fn() => route('items.show', $this->record)),
            ...parent::getActions()
        ];
    }
}
