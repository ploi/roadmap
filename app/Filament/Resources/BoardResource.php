<?php

namespace App\Filament\Resources;

use App\Models\Board;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\BoardResource\Pages;

class BoardResource extends Resource
{
    protected static ?string $model = Board::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

	public static function getNavigationGroup(): ?string {
		return trans('nav.content');
	}

    protected static bool $shouldRegisterNavigation = false;

    public static function getModelLabel(): string
    {
        return trans('resources.board.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('resources.board.label-plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                       ->schema([

                            TextInput::make('title')
                                ->label(trans('resources.board.title'))
                                ->required()
                                ->maxLength(255),

                            Select::make('project_id')
                                ->label(trans('resources.board.project'))
                                  ->relationship('project', 'title')
                                  ->required(),

                            Textarea::make('description')
                                ->label(trans('resources.board.description'))
                                ->columnSpan(2)
                                ->maxLength(65535),

                           ])->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),

                TextColumn::make('title')
                          ->label(trans('resources.board.title')),

                TextColumn::make('project.title')
                          ->label(trans('resources.board.project')),

                TextColumn::make('created_at')
                          ->label(trans('resources.user.created-at'))
                          ->dateTime()
                          ->sortable(),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListBoards::route('/'),
            'create' => Pages\CreateBoard::route('/create'),
            'edit' => Pages\EditBoard::route('/{record}/edit'),
        ];
    }
}
