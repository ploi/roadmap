<?php

namespace App\Filament\Resources\Inboxes\Pages;

use App\Models\Item;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Inboxes\InboxResource;

class ListInboxes extends ListRecords
{
    protected static string $resource = InboxResource::class;

    protected function getTableQuery(): Builder
    {
        return Item::query()->forInbox();
    }
}
