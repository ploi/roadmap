<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Item;
use Filament\Tables;
use App\Enums\UserRole;
use App\Models\Project;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers\VotesRelationManager;
use App\Filament\Resources\ItemResource\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\ItemResource\RelationManagers\ActivitiesRelationManager;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive';

    protected static ?string $navigationGroup = 'Manage';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
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
                                Forms\Components\MarkdownEditor::make('content')
                                    ->columnSpan(2)
                                    ->required()
                                    ->minLength(5)
                                    ->maxLength(65535),
                            ])->columns(2),

                        Tabs\Tab::make('Management')
                            ->schema([
                                Forms\Components\Toggle::make('private')
                                    ->helperText('Private items will only be visible to admins and employees')
                                    ->label('Private')
                                    ->default(false),
                                Forms\Components\MultiSelect::make('assigned_users')
                                    ->helperText('Assign admins/employees to items here.')
                                    ->preload()
                                    ->relationship('assignedUsers', 'name', fn (Builder $query) => $query->whereIn('role', [UserRole::Admin, UserRole::Employee])),
                            ]),
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
                    Forms\Components\Toggle::make('pinned')
                        ->helperText('Pinned items will always stay at top.')
                        ->label('Pinned')
                        ->default(false),
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
                Tables\Columns\TextColumn::make('title')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('total_votes')->label('Votes')->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('comments_count')->label('Comments')->counts('comments')->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('project.title'),
                Tables\Columns\TextColumn::make('board.title'),
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TagsColumn::make('assignedUsers.name')->visible(auth()->user()->hasRole(UserRole::Admin))->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Date'),
                Tables\Columns\BooleanColumn::make('pinned')->label('Pinned'),
                Tables\Columns\BooleanColumn::make('private')->label('Private')->toggleable()->toggledHiddenByDefault(),
            ])
            ->filters([
                Filter::make('assigned')
                    ->label('Assigned to me')
                    ->default(auth()->user()->hasRole(UserRole::Employee))
                    ->query(fn (Builder $query): Builder => $query->whereHas('assignedUsers', function ($query) {
                        return $query->where('user_id', auth()->id());
                    })),

                Filter::make('created_at')
                    ->form([
                        Forms\Components\Select::make('project_id')
                            ->label(trans('table.project'))
                            ->reactive()
                            ->options(Project::pluck('title', 'id')),
                        Forms\Components\Select::make('board_id')
                            ->label(trans('table.board'))
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
                                fn (Builder $query, $boardId): Builder => $query->where('board_id', $boardId),
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
