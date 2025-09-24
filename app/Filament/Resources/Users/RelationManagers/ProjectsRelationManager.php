<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\AttachAction;
use Filament\Forms\Components\Select;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(
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
                AttachAction::make()
                    ->recordSelectOptionsQuery(fn (Builder $query): Builder => $query->where('private', true))
                    ->recordSelect(fn (Select $select) => $select->helperText(__('projects.select-hidden-projects')))
                    ->inverseRelationshipName('members')
                    ->preloadRecordSelect(),
                ]
            )
            ->recordActions(
                [
                DetachAction::make(),
                ]
            )
            ->toolbarActions(
                [
                DetachBulkAction::make(),
                ]
            );
    }
}
