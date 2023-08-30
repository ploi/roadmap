<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class VotesRelationManager extends RelationManager
{
    protected static string $relationship = 'votes';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('subscribed')
                    ->label('Subscribed')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('model.title')->label('Item'),
                Tables\Columns\TextColumn::make('model.project.title')->label('Project'),
                Tables\Columns\BooleanColumn::make('subscribed')->label('Subscribed'),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function canCreate(): bool
    {
        return false;
    }
}
