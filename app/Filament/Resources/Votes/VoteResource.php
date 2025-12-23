<?php

namespace App\Filament\Resources\Votes;

use App\Models\Vote;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\Votes\Pages\EditVote;
use App\Filament\Resources\Votes\Pages\ListVotes;
use App\Filament\Resources\Votes\Pages\CreateVote;

class VoteResource extends Resource
{
    protected static ?string $model = Vote::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-hand-thumb-up';

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(trans('resources.user.label')),

                TextColumn::make('model.title')
                    ->label(trans('resources.vote.item'))
                    ->searchable(),

                IconColumn::make('subscribed')
                    ->label(trans('resources.vote.subscribed'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(trans('resources.created-at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                DeleteAction::make()->modalAlignment(Alignment::Left)
            ])
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
            'index' => ListVotes::route('/'),
            'create' => CreateVote::route('/create'),
            'edit' => EditVote::route('/{record}/edit'),
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
