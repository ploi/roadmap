<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\CommentResource;
use Closure;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\RelationManagers\RelationManager;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $recordTitleAttribute = 'content';

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Model $record): string => CommentResource::getUrl('edit', ['record' => $record]))
            ->columns([
                Tables\Columns\TextColumn::make('content')->searchable(),
                Tables\Columns\TextColumn::make('item.title'),
                Tables\Columns\TextColumn::make('votes_count')->counts('votes')->label(trans('table.total-votes'))->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->label('Date'),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}
