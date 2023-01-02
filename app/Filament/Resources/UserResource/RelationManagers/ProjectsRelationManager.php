<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\RelationManagers\RelationManager;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $recordTitleAttribute = 'title';

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return Project::query()->where('private', true)->exists();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordSelectOptionsQuery(fn (Builder $query): Builder => $query->where('private', true))
                    ->recordSelect(fn (Forms\Components\Select $select) => $select->helperText(__('projects.select-hidden-projects')))
                    ->inverseRelationshipName('members')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
