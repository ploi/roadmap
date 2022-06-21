<?php

namespace App\Filament\Resources\InboxResource\Pages;

use App\Models\Item;
use App\Filament\Resources\InboxResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListInboxes extends ListRecords
{
    protected static string $resource = InboxResource::class;

    protected function getTableQuery(): Builder
    {
        return Item::query()->forInbox();
    }
}
