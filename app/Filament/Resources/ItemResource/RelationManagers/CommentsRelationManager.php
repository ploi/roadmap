<?php

namespace App\Filament\Resources\ItemResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\CommentResource;
use Filament\Resources\RelationManagers\RelationManager;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $recordTitleAttribute = 'content';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('resources.comment.label-plural');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Model $record): string => CommentResource::getUrl('edit', [ 'record' => $record ]))
            ->columns(
                [
                TextColumn::make('content')
                    ->label(trans('resources.comment.content'))
                    ->searchable()
                    ->wrap(),

                TextColumn::make('user.name')
                    ->label(trans('resources.user.label')),

                TextColumn::make('votes_count')
                    ->counts('votes')
                    ->label(trans('table.total-votes'))
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(trans('resources.created-at'))
                    ->dateTime()
                    ->sortable(),
                ]
            )
            ->filters(
                [
                //
                ]
            )
            ->defaultSort('created_at', 'desc');
    }
}
