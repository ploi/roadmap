<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Models\Item;
use App\Filament\Resources\ItemResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getTableQuery(): Builder
    {
        return Item::query()
            ->where(function (Builder $query) {
                return $query->whereHas('board')->orWhereHas('project');
            });
    }
}
