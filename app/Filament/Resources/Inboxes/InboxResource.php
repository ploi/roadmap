<?php

namespace App\Filament\Resources\Inboxes;

use Filament\Schemas\Schema;
use App\Filament\Resources\Inboxes\Pages\ListInboxes;
use App\Filament\Resources\Inboxes\Pages\CreateInbox;
use App\Filament\Resources\Inboxes\Pages\EditInbox;
use Exception;
use App\Models\Item;
use App\Models\Project;
use Filament\Tables\Table;
use App\Enums\InboxWorkflow;
use Filament\Resources\Resource;
use App\Settings\GeneralSettings;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\InboxResource\Pages;
use App\Filament\Resources\Items\ItemResource;
use App\Filament\Resources\Items\RelationManagers\VotesRelationManager;
use App\Filament\Resources\Items\RelationManagers\CommentsRelationManager;

class InboxResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-inbox';
    protected static ?int $navigationSort = 100;

    protected static ?string $slug = 'inbox';

    public static function getNavigationLabel(): string
    {
        return trans('nav.inbox');
    }

    public static function getModelLabel(): string
    {
        return trans('resources.inbox.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('resources.inbox.label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return app(GeneralSettings::class)->getInboxWorkflow() != InboxWorkflow::Disabled;
    }

    public static function getNavigationBadge(): ?string
    {
        return Item::query()->forInbox()->count();
    }

    public static function form(Schema $schema): Schema
    {
        return ItemResource::form($schema);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                TextColumn::make('title')
                    ->label(trans('resources.item.title'))
                    ->wrap()
                    ->searchable(),

                TextColumn::make('project.title')
                    ->label(trans('resources.item.project'))
                    ->visible(app(GeneralSettings::class)->getInboxWorkflow() === InboxWorkflow::WithoutBoard),

                TextColumn::make('comments_count')
                    ->label(ucfirst(trans_choice('messages.comments', 2)))
                    ->counts('comments')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('votes_count')
                    ->label(ucfirst(trans_choice('messages.votes', 2)))
                    ->counts('votes')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label(trans('resources.item.user')),

                TextColumn::make('created_at')
                    ->label(trans('resources.created-at'))
                    ->dateTime()
                    ->sortable(),
                ]
            )
            ->filters(
                [
                Filter::make('item_filters')
                    ->schema(
                        [
                          Select::make('project_id')
                              ->label(trans('resources.item.project'))
                              ->reactive()
                              ->options(Project::pluck('title', 'id')),
                          ]
                    )
                    ->query(
                        function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['project_id'],
                                    fn (Builder $query, $projectId): Builder => $query->where(
                                        'project_id',
                                        $projectId
                                    ),
                                );
                        }
                    )
                ]
            )
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        // Use the same relationmanagers as the ItemResource since they are the same
        return [
            CommentsRelationManager::class,
            VotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListInboxes::route('/'),
            'create' => CreateInbox::route('/create'),
            'edit'   => EditInbox::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
