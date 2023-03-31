<?php

namespace App\Filament\Resources;

use App\Services\GitHubService;
use Filament\Forms;
use Filament\Tables;
use App\Models\Board;
use App\Models\Project;
use App\Services\Icons;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use App\Settings\GeneralSettings;
use App\Filament\Resources\ProjectResource\Pages;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';

    protected static ?string $navigationGroup = 'Manage';

    public static function form(Form $form): Form
    {
        $gitHubService = (new GitHubService);

        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\TextInput::make('title')
                        ->columnSpan(1)
                        ->required()
                        ->maxLength(255),
// For now, we're not using this..
//                    Forms\Components\TextInput::make('url')
//                        ->columnSpan(1)
//                        ->maxLength(255),
                    Forms\Components\TextInput::make('group')
                        ->helperText('Type a group here to categorise them in your roadmap')
                        ->columnSpan(1)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->helperText('Leave blank to generate one automatically')
                        ->columnSpan(1)
                        ->maxLength(255),
                    Forms\Components\Select::make('icon')
                        ->options(Icons::all())
                        ->searchable(),
                    Forms\Components\Toggle::make('private')
                        ->reactive()
                        ->default(false)
                        ->helperText('Private projects are only visible for employees and admins'),
                    Forms\Components\Select::make('repo')
                        ->label('GitHub repository')
                        ->visible($gitHubService->isEnabled())
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search) => $gitHubService->getRepositories($search)),
                    Forms\Components\Select::make('members')
                        ->multiple()
                        ->preload()
                        ->relationship('members', 'name')
                        ->visible(fn ($get) => (bool) $get('private'))
                        ->helperText('Allow certain users to view this project'),
                    Forms\Components\MarkdownEditor::make('description')
                        ->columnSpan(2)
                        ->maxLength(65535),
                    Forms\Components\Repeater::make('boards')
                        ->collapsible()
                        //->collapsed() // We can enable this when Filament has a way to set header titles
                        ->relationship('boards')
                        ->orderable('sort_order')
                        ->default(app(GeneralSettings::class)->default_boards)
                        ->columnSpan(2)
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Toggle::make('visible')->default(true)->helperText('Hides the board from the public view, but will still be accessible if you use the direct URL.'),
                                Forms\Components\Toggle::make('can_users_create')->helperText('Allow users to create items in this board.'),
                                Forms\Components\Toggle::make('block_comments')->helperText('Block users from commenting to items in this board.'),
                                Forms\Components\Toggle::make('block_votes')->helperText('Block users from voting to items in this board.'),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('title')->required(),
                                Forms\Components\Select::make('sort_items_by')
                                    ->options([
                                        Board::SORT_ITEMS_BY_POPULAR => 'Popular',
                                        Board::SORT_ITEMS_BY_LATEST => 'Latest',
                                    ])
                                    ->default(Board::SORT_ITEMS_BY_POPULAR)
                                    ->required(),
                            ]),

                            Forms\Components\Textarea::make('description')->helperText('Used as META description for SEO purposes.'),
                        ]),
                ])->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('boards_count')->counts('boards'),
                Tables\Columns\BooleanColumn::make('private'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Date'),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
