<?php

namespace App\Filament\Widgets;

use Closure;
use App\Models\Item;
use Filament\Tables;
use App\Filament\Resources\ItemResource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestItems extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        return Item::query()->latest()->limit(5);
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
