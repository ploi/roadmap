<?php

namespace App\Filament\Resources\ItemResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\RelationManagers\RelationManager;

class VotesRelationManager extends RelationManager
{
    protected static string $relationship = 'votes';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('resources.vote.label-plural');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                Select::make('user_id')
                    ->label(trans('resources.user.label'))
                    ->relationship('user', 'name')
                    ->preload()
                    ->required()
                    ->searchable(),

                Toggle::make('subscribed')
                    ->label(trans('resources.vote.subscribed'))
                    ->default(true),
                ]
            );
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                TextColumn::make('user.name')
                          ->label(trans('resources.user.label')),

                TextColumn::make('created_at')
                    ->label(trans('resources.created-at'))
                    ->dateTime()
                    ->sortable(),

                IconColumn::make('subscribed')
                    ->label(trans('resources.vote.subscribed'))
                    ->boolean(),
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
