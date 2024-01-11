<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ItemResource;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestItems extends BaseWidget
{
    protected function getTableHeading(): string | Htmlable | null
    {
        return trans('widgets.latest-items');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Item::latest()->visibleForCurrentUser()->limit(5))
            ->paginated(false)
            ->columns(
                [
                Tables\Columns\TextColumn::make('title')
                                         ->label(trans('widgets.title')),
                Tables\Columns\TextColumn::make('total_votes')
                                         ->label(trans('widgets.total-votes')),
                Tables\Columns\TextColumn::make('project.title')
                                         ->label(trans('widgets.project')),
                Tables\Columns\TextColumn::make('board.title')
                                         ->label(trans('widgets.board')),
                ]
            )
            ->recordUrl(fn ($record) => ItemResource::getUrl('edit', ['record' => $record]));
    }
}
