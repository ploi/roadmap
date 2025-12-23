<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\Items\ItemResource;
use Filament\Widgets\TableWidget as BaseWidget;

class PopularItems extends BaseWidget
{
    protected function getTableHeading(): string | Htmlable | null
    {
        return trans('widgets.popular-items');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Item::popular()->visibleForCurrentUser()->limit(5))
            ->paginated(false)
            ->columns(
                [
                TextColumn::make('title')
                    ->label(trans('widgets.title')),
                TextColumn::make('total_votes')
                    ->label(trans('widgets.total-votes')),
                TextColumn::make('project.title')
                    ->label(trans('widgets.project')),
                TextColumn::make('board.title')
                    ->label(trans('widgets.board')),
                ]
            )
            ->recordUrl(fn ($record) => ItemResource::getUrl('edit', ['record' => $record]));
    }
}
