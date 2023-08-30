<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Item;
use Filament\Tables;
use App\Models\Project;
use App\Enums\InboxWorkflow;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Settings\GeneralSettings;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\InboxResource\Pages;
use App\Filament\Resources\InboxResource\RelationManagers\VotesRelationManager;
use App\Filament\Resources\InboxResource\RelationManagers\CommentsRelationManager;

class InboxResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationGroup = 'Manage';

    protected static ?string $label = 'Inbox';

    protected static ?string $pluralLabel = 'Inbox';

    protected static ?string $slug = 'inbox';

    public static function shouldRegisterNavigation(): bool
    {
        return app(GeneralSettings::class)->getInboxWorkflow() != InboxWorkflow::Disabled;
    }

    public static function getNavigationBadge(): ?string
    {
        return Item::query()->forInbox()->count();
    }

    public static function form(Form $form): Form
    {
        return ItemResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->wrap()->searchable(),
                Tables\Columns\TextColumn::make('project.title')
                    ->visible(app(GeneralSettings::class)->getInboxWorkflow() === InboxWorkflow::WithoutBoard),
                Tables\Columns\TextColumn::make('comments_count')->label(ucfirst(trans_choice('messages.comments', 2)))->counts('comments')->toggleable(),
                Tables\Columns\TextColumn::make('votes_count')->label(ucfirst(trans_choice('messages.votes', 2)))->counts('votes')->toggleable(),
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Date'),
            ])
            ->filters([
                Filter::make('item_filters')
                    ->form([
                        Forms\Components\Select::make('project_id')
                            ->label(trans('table.project'))
                            ->reactive()
                            ->options(Project::pluck('title', 'id')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['project_id'],
                                fn (Builder $query, $projectId): Builder => $query->where('project_id', $projectId),
                            );
                    })
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
            VotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInboxes::route('/'),
            'create' => Pages\CreateInbox::route('/create'),
            'edit' => Pages\EditInbox::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
