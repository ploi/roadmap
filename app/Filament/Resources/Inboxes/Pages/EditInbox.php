<?php

namespace App\Filament\Resources\Inboxes\Pages;

use App\Filament\Resources\Inboxes\InboxResource;
use App\Filament\Resources\Items\Pages\EditItem;

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
