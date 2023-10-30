<?php

namespace App\Filament\Resources\InboxResource\Pages;

use App\Filament\Resources\InboxResource;
use App\Filament\Resources\ItemResource\Pages\EditItem;

class EditInbox extends EditItem
{
    protected static string $resource = InboxResource::class;

    public function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions()
        ];
    }
}
