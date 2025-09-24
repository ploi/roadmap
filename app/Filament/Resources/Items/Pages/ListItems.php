<?php

namespace App\Filament\Resources\Items\Pages;

use App\Models\Item;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Items\ItemResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Item::query()
            ->where(
                function (Builder $query) {
                    return $query->whereHas('board')->orWhereHas('project');
                }
            );
    }
}
