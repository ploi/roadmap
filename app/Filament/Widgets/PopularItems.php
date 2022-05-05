<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ItemResource;
use App\Models\Item;
use Closure;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PopularItems extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        return Item::query()->popular()->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('board.title'),
            Tables\Columns\TextColumn::make('board.project.title')
                ->label('Project'),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn ($record) => ItemResource::getUrl('edit', $record);
    }
}
