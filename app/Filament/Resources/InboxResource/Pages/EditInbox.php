<?php

namespace App\Filament\Resources\InboxResource\Pages;

use Filament\Pages\Actions\Action;
use App\Filament\Resources\InboxResource;
use App\Filament\Resources\ItemResource\Pages\EditItem;

class EditInbox extends EditItem
{
    protected static string $resource = InboxResource::class;

    public function getActions(): array
    {
        return [
            Action::make('view_public')->color('secondary')->url(fn () => route('items.show', $this->record)),
            ...parent::getActions()
        ];
    }
}
