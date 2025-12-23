<?php

namespace App\Filament\Resources\Items;

use Exception;
use Throwable;
use Filament\Forms;
use App\Models\Item;
use App\Models\User;
use Filament\Tables;
use App\Enums\UserRole;
use App\Models\Project;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Services\GitHubService;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Resources\Items\Pages\EditItem;
use App\Filament\Resources\Items\Pages\ListItems;
use App\Filament\Resources\Items\Pages\CreateItem;
use App\Filament\Resources\Items\RelationManagers\VotesRelationManager;
use App\Filament\Resources\Items\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\Items\RelationManagers\ActivitiesRelationManager;
use App\Filament\Resources\Items\RelationManagers\ChangelogsRelationManager;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-archive-box';


    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 100;

    public static function getNavigationGroup(): ?string
    {
        return trans('nav.content');
    }

    public static function getNavigationLabel(): string
    {
        return trans('nav.item');
    }

    public static function getModelLabel(): string
    {
        return trans('resources.item.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('resources.item.label-plural');
    }

    public static function form(Schema $schema): Schema
    {
        $gitHubService = (new GitHubService);

        return $schema
            ->components(
                [
                Tabs::make(trans('resources.item.heading'))
                    ->columnSpan(3)
                    ->tabs(
                        [
                        Tab::make(trans('resources.item.label'))
                            ->schema(
                                [

                                    TextInput::make('title')
                                        ->label(trans('resources.item.title'))
                                        ->required()
                                        ->maxLength(255),

                                    Select::make('user_id')
                                        ->label(trans('resources.item.user'))
                                        ->relationship('user', 'name')
                                        ->default(auth()->user()->id)
                                        ->preload()
                                        ->required()
                                        ->searchable(),

                                    TextInput::make('slug')
                                        ->label(trans('resources.item.slug'))
                                        ->required()
                                        ->hiddenOn('create')
                                        ->maxLength(255),

                                    Select::make('issue_number')
                                        ->label(trans('resources.item.github.issue'))
                                        ->visible(
                                            fn (
                                                $record
                                            ) => $record?->project?->repo && $gitHubService->isEnabled()
                                        )
                                        ->searchable()
                                        ->getSearchResultsUsing(
                                            fn (
                                                string $search,
                                                $record
                                            ) => $gitHubService->getIssuesForRepository($record?->project->repo)
                                        )
                                        ->getOptionLabelUsing(
                                            fn (
                                                $record,
                                                Get $get
                                            ) => $gitHubService->getIssueTitle(
                                                $record?->project->repo,
                                                $get('issue_number')
                                            )
                                        )
                                        ->reactive()
                                        ->suffixAction(
                                            function (Get $get, Set $set, $record) {
                                                if (blank($record?->project->repo) || filled($get('issue_number'))) {
                                                    return null;
                                                }

                                                return Action::make('github-create-issue')
                                                    ->icon('heroicon-s-plus')
                                                    ->tooltip(trans('resources.item.github.create'))
                                                    ->modalHeading(trans('resources.item.github.create-new'))
                                                    ->modalSubmitActionLabel(trans('resources.item.github-issue-create'))
                                                    ->schema(
                                                        [
                                                                                    Grid::make()
                                                                                        ->schema(
                                                                                            [
                                                                                            Select::make('repo')
                                                                                                ->label(trans('resources.item.github.repository'))
                                                                                                ->default($record->project->repo)
                                                                                                ->searchable()
                                                                                                ->getSearchResultsUsing(
                                                                                                    fn (
                                                                                                        string $search
                                                                                                    ) => (new GitHubService)->getRepositories($search)
                                                                                                ),

                                                                                            TextInput::make('title')
                                                                                                ->label(trans('resources.item.github.title'))
                                                                                                ->default($record->title),
                                                                                            ]
                                                                                        ),

                                                                                    MarkdownEditor::make('body')
                                                                                        ->label(trans('resources.item.github.body'))
                                                                                        ->columnSpan(2)
                                                                                        ->default($record->content)
                                                                                        ->minLength(5)
                                                                                        ->maxLength(65535),

                                                                                  ]
                                                    )
                                                    ->action(
                                                        function ($data) use (
                                                            $set,
                                                            $record
                                                        ) {
                                                            try {
                                                                $issueNumber = (new GitHubService)->createIssueInRepository(
                                                                    $data['repo'],
                                                                    $data['title'],
                                                                    $data['body'] . PHP_EOL . PHP_EOL . route(
                                                                        'items.show',
                                                                        $record->slug
                                                                    )
                                                                );
                                                            } catch (Throwable $exception) {
                                                                Notification::make()
                                                                    ->title(trans('resources.item.github.label'))
                                                                    ->body($exception->getMessage())
                                                                    ->danger()
                                                                    ->send();

                                                                return;
                                                            }

                                                            $set(
                                                                'issue_number',
                                                                $issueNumber
                                                            );

                                                            $record->issue_number = $issueNumber;
                                                            $record->save();

                                                            Notification::make()
                                                                ->title(trans('resources.item.github.label'))
                                                                ->body(
                                                                    sprintf(
                                                                        trans('resources.item.github.created-text'),
                                                                        $issueNumber,
                                                                        $data['repo']
                                                                    )
                                                                )
                                                                ->actions(
                                                                    [
                                                                        Action::make('kots')
                                                                            ->button()
                                                                            ->url("https://github.com/{$data['repo']}/issues/{$issueNumber}")
                                                                            ->label(trans('resources.item.github.view'))
                                                                            ->openUrlInNewTab()
                                                                        ]
                                                                )
                                                                ->success()
                                                                ->send();
                                                        }
                                                    );
                                            }
                                        )
                                        ->hintAction(
                                            function ($get, $record) {
                                                if (blank($record?->project?->repo) || blank($get('issue_number'))) {
                                                    return null;
                                                }

                                                return Action::make('github-link')
                                                    ->label(trans('resources.item.github.view'))
                                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                                    ->extraAttributes([ 'class' => 'w-5 h-5' ])
                                                    ->url("https://github.com/{$record->project->repo}/issues/{$get('issue_number')}")
                                                    ->openUrlInNewTab();
                                            }
                                        ),
                                    MarkdownEditor::make('content')
                                        ->label(trans('resources.item.content'))
                                        ->columnSpan(2)
                                        ->required()
                                        ->minLength(5)
                                        ->maxLength(65535),
                                //                                Forms\Components\SpatieTagsInput::make('tags')
                                //                                    ->translateLabel()
                                //                                    ->columnSpan(2),
                                    ]
                            )->columns(),

                        Tab::make('Management')
                            ->schema(
                                [
                                    Toggle::make('pinned')
                                        ->label(trans('resources.item.pinned'))
                                        ->helperText(trans('resources.item.pinned-helper-text'))
                                        ->default(false),

                                    Toggle::make('private')
                                        ->label(trans('resources.item.private'))
                                        ->helperText(trans('resources.item.private-helper-text'))
                                        ->default(false),

                                    Select::make('assigned_users')
                                        ->label(trans('resources.item.users'))
                                        ->helperText(trans('resources.item.users-helper-text'))
                                        ->multiple()
                                        ->preload()
                                        ->relationship(
                                            'assignedUsers',
                                            'name',
                                            fn (Builder $query) => $query->whereIn(
                                                'role',
                                                [ UserRole::Admin, UserRole::Employee ]
                                            )
                                        )
                                        ->columnSpan(2),
                                    ]
                            )->columns(),
                        ]
                    ),

                Section::make()
                    ->columnSpan(1)
                    ->schema(
                        [
                                            Select::make('project_id')
                                                ->label(trans('resources.item.project'))
                                                ->options(Project::query()->pluck('title', 'id'))
                                                ->reactive()
                                                ->required(),

                                            Select::make('board_id')
                                                ->label(trans('resources.item.board'))
                                                ->required()
                                                ->options(
                                                    fn (
                                                        Get $get
                                                    ) => Project::find($get('project_id'))?->boards()->pluck(
                                                        'title',
                                                        'id'
                                                    ) ?? []
                                                ),

                                            Toggle::make('notify_subscribers')
                                                ->label(trans('resources.item.notify-subscribers'))
                                                ->helperText(trans('resources.item.notify-subscribers-helper-text'))
                                                ->default(true),

                                            Placeholder::make('created_at')
                                                ->label(trans('resources.created-at'))
                                                ->visible(fn ($record) => filled($record))
                                                ->content(
                                                    fn (
                                                        $record
                                                    ) => $record->created_at->format('d-m-Y H:i:s')
                                                ),

                                            Placeholder::make('updated_at')
                                                ->label(trans('resources.updated-at'))
                                                ->visible(fn ($record) => filled($record))
                                                ->content(
                                                    fn (
                                                        $record
                                                    ) => $record->updated_at->format('d-m-Y H:i:s')
                                                ),

                                            ]
                    )
                ]
            )
            ->columns(4);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                TextColumn::make('id')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('slug')
                    ->label(trans('resources.item.slug'))
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('title')
                    ->label(trans('resources.item.title'))
                    ->searchable()
                    ->wrap(),

                TextColumn::make('total_votes')
                    ->label(trans('resources.item.votes'))
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('comments_count')
                    ->label(trans('resources.comment.label-plural'))
                    ->counts('comments')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('project.title')
                    ->label(trans('resources.project.label'))
                    ->sortable(),

                TextColumn::make('board.title')
                    ->label(trans('resources.board.label'))
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(trans('resources.user.label'))
                    ->toggleable(),

                //                Tables\Columns\TagsColumn::make('tags.name')->toggleable()->toggledHiddenByDefault(),

                TextColumn::make('assignedUsers.name')
                    ->label(trans('resources.item.users'))
                    ->badge()
                    ->visible(auth()->user()->hasRole(UserRole::Admin))
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('created_at')
                    ->label(trans('resources.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('pinned')
                    ->label(trans('resources.item.pinned'))
                    ->boolean()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                IconColumn::make('private')
                    ->label(trans('resources.item.private'))
                    ->boolean()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                ]
            )
            ->filters(
                [
                Filter::make('assigned')
                    ->label(trans('resources.item.assigned-to-me'))
                    ->default(auth()->user()->hasRole(UserRole::Employee))
                    ->query(
                        fn (Builder $query): Builder => $query->whereHas(
                            'assignedUsers',
                            function ($query) {
                                return $query->where('user_id', auth()->id());
                            }
                        )
                    ),

                Filter::make('assignees')
                    ->label(trans('resources.item.assigness'))
                    ->schema(
                        [
                          Select::make('users')
                              ->label(trans('resources.item.assigned-to'))
                              ->multiple()
                              ->options(
                                  User::query()
                                    ->whereIn(
                                        'role',
                                        [
                                            UserRole::Employee->value,
                                            UserRole::Admin->value
                                            ]
                                    )
                                    ->pluck('name', 'id')
                              )
                          ]
                    )
                    ->query(
                        function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['users'],
                                    fn (Builder $query, $users): Builder => $query->whereHas(
                                        'assignedUsers',
                                        function ($query) use ($users) {
                                            return $query->whereIn('users.id', $users);
                                        }
                                    ),
                                );
                        }
                    ),

                Filter::make('item_filters')
                    ->label(trans('resources.item.item-filters'))
                    ->schema(
                        [
                          Select::make('project_id')
                              ->label(trans('resources.project.label'))
                              ->afterStateUpdated(
                                  function (Set $set, Get $get) {
                                      if ($get('board_id')) {
                                          $set('board_id', null);
                                      }
                                  }
                              )
                                ->reactive()
                                ->options(Project::pluck('title', 'id')),

                          Select::make('board_id')
                              ->label(trans('resources.board.label'))
                              ->multiple()
                              ->preload()
                              ->options(
                                  fn (Get $get) => Project::find($get('project_id'))?->boards()->pluck(
                                      'title',
                                      'id'
                                  ) ?? []
                              ),

                          Toggle::make('pinned')
                              ->label(trans('resources.item.pinned')),

                          Toggle::make('private')
                              ->label(trans('resources.item.private')),
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
                                )
                                ->when(
                                    $data['board_id'],
                                    fn (Builder $query, $boardIds): Builder => $query->whereHas(
                                        'board',
                                        function ($query) use ($boardIds) {
                                            return $query->whereIn('id', $boardIds);
                                        }
                                    ),
                                )
                            ->when(
                                $data['pinned'],
                                fn (Builder $query): Builder => $query->where('pinned', $data['pinned']),
                            )
                            ->when(
                                $data['private'],
                                fn (Builder $query): Builder => $query->where('private', $data['private']),
                            );
                        }
                    )

                ]
            )
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
            CommentsRelationManager::class,
            VotesRelationManager::class,
            ChangelogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListItems::route('/'),
            'create' => CreateItem::route('/create'),
            'edit' => EditItem::route('/{record}/edit'),
        ];
    }
}
