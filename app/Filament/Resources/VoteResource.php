<?php

namespace App\Filament\Resources;

use App\Models\Vote;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\VoteResource\Pages;

class VoteResource extends Resource
{
    protected static ?string $model = Vote::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-thumb-up';

    protected static ?int $navigationSort = 300;

    public static function getNavigationGroup(): ?string
    {
        return trans('nav.content');
    }

    public static function getNavigationLabel(): string
    {
        return trans('nav.votes');
    }

    public static function getModelLabel(): string
    {
        return trans('resources.vote.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('resources.vote.label-plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                //
                ]
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                TextColumn::make('user.name')
                          ->label(trans('resources.user.label')),

                TextColumn::make('model.title')
                          ->label(trans('resources.vote.item')),

                IconColumn::make('subscribed')
                    ->label(trans('resources.vote.subscribed'))
                    ->boolean(),

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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVotes::route('/'),
            'create' => Pages\CreateVote::route('/create'),
            'edit'   => Pages\EditVote::route('/{record}/edit'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
