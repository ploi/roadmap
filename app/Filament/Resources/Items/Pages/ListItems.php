<?php

namespace App\Filament\Resources\Items\Pages;

use App\Models\Item;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Items\ItemResource;

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
