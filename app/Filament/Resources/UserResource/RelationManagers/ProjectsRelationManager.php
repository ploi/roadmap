<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\RelationManagers\RelationManager;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('resources.project.label-plural');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return Project::query()->where('private', true)->exists();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                TextInput::make('name')
                    ->label(trans('resources.project.name'))
                    ->required()
                    ->maxLength(255),
                ]
            );
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                TextColumn::make('title')
                    ->label(trans('resources.project.title')),
                ]
            )
            ->filters(
                [
                //
                ]
            )
            ->headerActions(
                [
                Tables\Actions\AttachAction::make()
                    ->recordSelectOptionsQuery(fn (Builder $query): Builder => $query->where('private', true))
                    ->recordSelect(fn (Forms\Components\Select $select) => $select->helperText(__('projects.select-hidden-projects')))
                    ->inverseRelationshipName('members')
                    ->preloadRecordSelect(),
                ]
            )
            ->actions(
                [
                Tables\Actions\DetachAction::make(),
                ]
            )
            ->bulkActions(
                [
                Tables\Actions\DetachBulkAction::make(),
                ]
            );
    }
}
