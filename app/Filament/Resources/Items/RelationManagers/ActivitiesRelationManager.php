<?php

namespace App\Filament\Resources\Items\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\RelationManagers\RelationManager;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $recordTitleAttribute = 'description';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('resources.item.activities');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                [
                //
                ]
            );
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                TextColumn::make('causer.name')
                          ->label(trans('resources.user.label')),

                TextColumn::make('description')
                          ->label(trans('resources.item.description')),

                TextColumn::make('created_at')
                    ->label(trans('resources.date'))
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

    protected function canEdit(Model $record): bool
    {
        return false;
    }

    protected function canDelete(Model $record): bool
    {
        return false;
    }

    public function canCreate(): bool
    {
        return false;
    }
}
