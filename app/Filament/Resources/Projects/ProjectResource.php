<?php

namespace App\Filament\Resources\Projects;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use Filament\Forms;
use App\Models\Board;
use App\Models\Project;
use App\Services\Icons;
use Filament\Tables\Table;
use App\Services\GitHubService;
use Filament\Resources\Resource;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\ProjectResource\Pages;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 1100;

    public static function getNavigationGroup(): ?string
    {
        return trans('nav.manage');
    }


    public static function getNavigationLabel(): string
    {
        return trans('nav.project');
    }

    public static function getModelLabel(): string
    {
        return trans('resources.project.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('resources.project.label-plural');
    }

    public static function form(Schema $schema): Schema
    {
        $gitHubService = (new GitHubService);

        return $schema
            ->components([
                Section::make()
                    ->columns()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label(trans('resources.project.title'))
                            ->columnSpan(1)
                            ->required()
                            ->maxLength(255),
// For now, we're not using this..
//                    Forms\Components\TextInput::make('url')
//                        ->columnSpan(1)
//                        ->maxLength(255),
                        TextInput::make('group')
                            ->label(trans('resources.project.group'))
                            ->helperText(trans('resources.project.group-helper-text'))
                            ->columnSpan(1)
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label(trans('resources.project.slug'))
                            ->helperText(trans('resources.project.slug-helper-text'))
                            ->columnSpan(1)
                            ->maxLength(255),

                        Select::make('icon')
                            ->label(trans('resources.project.icon'))
                            ->options(Icons::all())
                            ->searchable(),

                        Toggle::make('private')
                            ->label(trans('resources.project.private'))
                            ->helperText(trans('resources.project.private-helper-text'))
                            ->reactive()
                            ->default(false),

                        Toggle::make('collapsible')
                            ->label(trans('resources.project.collapsible'))
                            ->helperText(trans('resources.project.collapsible-helper-text'))
                            ->default(false),

                        Select::make('repo')
                            ->label(trans('resources.project.github-repo'))
                            ->visible($gitHubService->isEnabled())
                            ->searchable()
                            ->getSearchResultsUsing(fn(
                                string $search
                            ) => $gitHubService->getRepositories($search)),

                        Select::make('members')
                            ->label(trans('resources.project.viewers'))
                            ->helperText(trans('resources.project.viewers-helper-text'))
                            ->multiple()
                            ->preload()
                            ->relationship('members', 'name')
                            ->visible(fn($get) => (bool)$get('private')),

                        MarkdownEditor::make('description')
                            ->label(trans('resources.project.description'))
                            ->columnSpan(2)
                            ->maxLength(65535),

                        Repeater::make('boards')
                            ->label(trans('resources.board.label-plural'))
                            ->collapsible()
                            ->collapsed()
                            ->relationship('boards')
                            ->orderColumn('sort_order')
                            ->default(app(GeneralSettings::class)->default_boards)
                            ->columnSpan(2)
                            ->itemLabel(fn($state) => $state['title'] ?? '')
                            ->schema([
                                Grid::make()->schema([
                                    Toggle::make('visible')
                                        ->label(trans('resources.board.visible'))
                                        ->helperText(trans('resources.board.visible-helper-text'))
                                        ->default(true),

                                    Toggle::make('can_users_create')
                                        ->label(trans('resources.board.user-can-create'))
                                        ->helperText(trans('resources.board.user-can-create-helper-text')),

                                    Toggle::make('block_comments')
                                        ->label(trans('resources.board.block-comments'))
                                        ->helperText(trans('resources.board.block-comments-helper-text')),

                                    Toggle::make('block_votes')
                                        ->label(trans('resources.board.block-votes'))
                                        ->helperText(trans('resources.board.block-votes-helper-text')),
                                ]),

                                Grid::make()->schema([
                                    TextInput::make('title')
                                        ->label(trans('resources.board.title'))
                                        ->required()
                                        ->live(),

                                    Select::make('sort_items_by')
                                        ->label(trans('resources.board.sort-items-by'))
                                        ->options([
                                            Board::SORT_ITEMS_BY_POPULAR => trans('resources.board.popular'),
                                            Board::SORT_ITEMS_BY_LATEST => trans('resources.board.latest'),
                                        ])
                                        ->default(Board::SORT_ITEMS_BY_POPULAR)
                                        ->required(),
                                ]),

                                Textarea::make('description')
                                    ->label(trans('resources.board.description'))
                                    ->helperText(trans('resources.board.description-helper-text')),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),

                TextColumn::make('title')
                    ->label(trans('resources.project.title'))
                    ->searchable(),

                TextColumn::make('boards_count')
                    ->label(trans('resources.board.label-plural'))
                    ->counts('boards'),

                IconColumn::make('private')
                    ->label(trans('resources.project.private'))
                    ->icon(fn(
                        $record
                    ) => $record->private ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open'),

                TextColumn::make('created_at')
                    ->label(trans('resources.created-at'))
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->defaultSort('sort_order');
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
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
