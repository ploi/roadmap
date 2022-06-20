<?php

namespace App\Filament\Widgets;

use Closure;
use App\Models\Item;
use Filament\Tables;
use App\Filament\Resources\ItemResource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class PopularItems extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        return Item::query()->visibleForCurrentUser()->popular()->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('total_votes'),
            Tables\Columns\TextColumn::make('project.title')
                ->label('Project'),
            Tables\Columns\TextColumn::make('board.title'),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn ($record) => ItemResource::getUrl('edit', $record);
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
