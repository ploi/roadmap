<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Item;
use App\Models\User;
use Filament\Tables;
use App\Enums\UserRole;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Services\GitHubService;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Actions\Action;
use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers\VotesRelationManager;
use App\Filament\Resources\ItemResource\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\ItemResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Resources\ItemResource\RelationManagers\ChangelogsRelationManager;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Manage';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        $gitHubService = (new GitHubService);

        return $form
            ->schema([
                Tabs::make('Heading')
                    ->tabs([
                        Tabs\Tab::make('Item')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->default(auth()->user()->id)
                                    ->preload()
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->hiddenOn('create')
                                    ->maxLength(255),
                                Forms\Components\Select::make('issue_number')
                                    ->label('GitHub issue')
                                    ->visible(fn ($record) => $record?->project?->repo && $gitHubService->isEnabled())
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search, $record) => $gitHubService->getIssuesForRepository($record?->project->repo))
                                    ->getOptionLabelUsing(fn ($record, \Filament\Forms\Get $get) => $gitHubService->getIssueTitle($record?->project->repo, $get('issue_number')))
                                    ->reactive()
                                    ->suffixAction(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set, $record) {
                                        if (blank($record?->project->repo) || filled($get('issue_number'))) {
                                            return null;
                                        }

                                        return Forms\Components\Actions\Action::make('github-create-issue')
                                            ->icon('heroicon-s-plus')
                                            ->tooltip('Create GitHub issue')
                                            ->modalHeading('Create new GitHub issue')
                                            ->modalButton('Create issue')
                                            ->form([
                                                Forms\Components\Grid::make(2)->schema([
                                                    Forms\Components\Select::make('repo')
                                                        ->label('Repository')
                                                        ->default($record->project->repo)
                                                        ->searchable()
                                                        ->getSearchResultsUsing(fn (string $search) => (new GitHubService)->getRepositories($search)),
                                                    Forms\Components\TextInput::make('title')
                                                        ->default($record->title),
                                                ]),

                                                Forms\Components\MarkdownEditor::make('body')
                                                    ->columnSpan(2)
                                                    ->default($record->content)
                                                    ->minLength(5)
                                                    ->maxLength(65535),
                                            ])
                                            ->action(function ($data) use ($set, $record) {
                                                try {
                                                    $issueNumber = (new GitHubService)->createIssueInRepository(
                                                        $data['repo'],
                                                        $data['title'],
                                                        $data['body'] . PHP_EOL . PHP_EOL . route('items.show', $record->slug)
                                                    );
                                                } catch (\Throwable $exception) {
                                                    Notification::make()
                                                        ->title("GitHub")
                                                        ->body($exception->getMessage())
                                                        ->danger()
                                                        ->send();

                                                    return;
                                                }

                                                $set('issue_number', $issueNumber);

                                                $record->issue_number = $issueNumber;
                                                $record->save();

                                                Notification::make()
                                                    ->title('GitHub')
                                                    ->body("Creates issue #{$issueNumber} in {$data['repo']}")
                                                    ->actions([
                                                        Action::make('kots')
                                                            ->button()
                                                            ->url("https://github.com/{$data['repo']}/issues/{$issueNumber}")
                                                            ->label('View issue')
                                                            ->openUrlInNewTab()
                                                    ])
                                                    ->success()
                                                    ->send();
                                            });
                                    })
                                    ->hintAction(function ($get, $record) {
                                        if (blank($record?->project->repo) || blank($get('issue_number'))) {
                                            return null;
                                        }

                                        return Forms\Components\Actions\Action::make('github-link')
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->extraAttributes(['class' => 'w-5 h-5'])
                                            ->url("https://github.com/{$record->project->repo}/issues/{$get('issue_number')}")
                                            ->openUrlInNewTab();
                                    }),
                                Forms\Components\MarkdownEditor::make('content')
                                    ->columnSpan(2)
                                    ->required()
                                    ->minLength(5)
                                    ->maxLength(65535),
//                                Forms\Components\SpatieTagsInput::make('tags')
//                                    ->translateLabel()
//                                    ->columnSpan(2),
                            ])->columns(2),

                        Tabs\Tab::make('Management')
                            ->schema([
                                Forms\Components\Toggle::make('pinned')
                                    ->helperText('Pinned items will always stay at top.')
                                    ->label('Pinned')
                                    ->default(false),
                                Forms\Components\Toggle::make('private')
                                    ->helperText('Private items will only be visible to admins and employees')
                                    ->label('Private')
                                    ->default(false),
                                Forms\Components\Select::make('assigned_users')
                                    ->multiple()
                                    ->helperText('Assign admins/employees to items here.')
                                    ->preload()
                                    ->relationship('assignedUsers', 'name', fn (Builder $query) => $query->whereIn('role', [UserRole::Admin, UserRole::Employee]))
                                    ->columnSpan(2),
                            ])->columns(),
                    ])->columnSpan(3),

                Forms\Components\Card::make([
                    Forms\Components\Select::make('project_id')
                        ->label('Project')
                        ->options(Project::query()->pluck('title', 'id'))
                        ->reactive()
                        ->required(),
                    Forms\Components\Select::make('board_id')
                        ->label('Board')
                        ->options(fn ($get) => Project::find($get('project_id'))?->boards()->pluck('title', 'id') ?? [])
                        ->required(),
                    Forms\Components\Toggle::make('notify_subscribers')
                        ->helperText('Send a notification with updates about the item')
                        ->label('Notify subscribers')
                        ->default(true),
                    Forms\Components\Placeholder::make('created_at')
                        ->label('Created at')
                        ->visible(fn ($record) => filled($record))
                        ->content(fn ($record) => $record->created_at->format('d-m-Y H:i:s')),
                    Forms\Components\Placeholder::make('updated_at')
                        ->label('Updated at')
                        ->visible(fn ($record) => filled($record))
                        ->content(fn ($record) => $record->updated_at->format('d-m-Y H:i:s')),
                ])->columnSpan(1),
            ])
            ->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->searchable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('slug')->searchable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('title')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('total_votes')->label('Votes')->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('comments_count')->label('Comments')->counts('comments')->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('project.title'),
                Tables\Columns\TextColumn::make('board.title')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->toggleable(),
//                Tables\Columns\TagsColumn::make('tags.name')->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('assignedUsers.name')->badge()->visible(auth()->user()->hasRole(UserRole::Admin))->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->label('Date'),
                Tables\Columns\BooleanColumn::make('pinned')->label('Pinned')->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\BooleanColumn::make('private')->label('Private')->sortable()->toggleable()->toggledHiddenByDefault(),
            ])
            ->filters([
                Filter::make('assigned')
                    ->label('Assigned to me')
                    ->default(auth()->user()->hasRole(UserRole::Employee))
                    ->query(fn (Builder $query): Builder => $query->whereHas('assignedUsers', function ($query) {
                        return $query->where('user_id', auth()->id());
                    })),

                Filter::make('assignees')
                    ->form([
                        Forms\Components\MultiSelect::make('users')
                            ->label('Assigned to')
                            ->options(
                                User::query()
                                    ->whereIn('role', [UserRole::Employee->value, UserRole::Admin->value])
                                    ->pluck('name', 'id')
                            )
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['users'],
                                fn (Builder $query, $users): Builder => $query->whereHas('assignedUsers', function ($query) use ($users) {
                                    return $query->whereIn('users.id', $users);
                                }),
                            );
                    }),

                Filter::make('item_filters')
                    ->form([
                        Forms\Components\Select::make('project_id')
                            ->label(trans('table.project'))
                            ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                                if ($get('board_id')) {
                                    $set('board_id', null);
                                }
                            })
                            ->reactive()
                            ->options(Project::pluck('title', 'id')),
                        Forms\Components\MultiSelect::make('board_id')
                            ->label(trans('table.board'))
                            ->preload()
                            ->options(fn ($get) => Project::find($get('project_id'))?->boards()->pluck('title', 'id') ?? []),
                        Forms\Components\Toggle::make('pinned')
                            ->label('Pinned'),
                        Forms\Components\Toggle::make('private')
                            ->label('Private'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['project_id'],
                                fn (Builder $query, $projectId): Builder => $query->where('project_id', $projectId),
                            )
                            ->when(
                                $data['board_id'],
                                fn (Builder $query, $boardIds): Builder => $query->whereHas('board', function ($query) use ($boardIds) {
                                    return $query->whereIn('id', $boardIds);
                                }),
                            )
                            ->when(
                                $data['pinned'],
                                fn (Builder $query): Builder => $query->where('pinned', $data['pinned']),
                            )
                            ->when(
                                $data['private'],
                                fn (Builder $query): Builder => $query->where('private', $data['private']),
                            );
                    })

            ])
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
