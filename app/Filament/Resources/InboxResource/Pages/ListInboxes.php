<?php

namespace App\Filament\Resources\InboxResource\Pages;

use App\Filament\Resources\InboxResource;
use App\Models\Item;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListInboxes extends ListRecords
{
    protected static string $resource = InboxResource::class;

    protected function getTableQuery(): Builder
    {
        return Item::query()->inbox();
    }
}
