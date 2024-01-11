<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ItemResource;
use Filament\Resources\RelationManagers\RelationManager;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('resources.item.label-plural');
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Model $record): string => ItemResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('id'),

                TextColumn::make('title')
                          ->label(trans('resources.item.title'))
                          ->searchable(),

                TextColumn::make('total_votes')
                          ->label(trans('resources.item.votes'))
                          ->sortable(),

                TextColumn::make('board.project.title')
                          ->label(trans('resources.item.project'))
                          ->sortable()
                          ->searchable(),

                TextColumn::make('board.title')
                          ->label(trans('resources.item.board'))
                          ->sortable()
                          ->searchable(),

                TextColumn::make('created_at')
                    ->label(trans('resources.created-at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}
