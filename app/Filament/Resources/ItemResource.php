<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Item;
use Filament\Tables;
use App\Models\Project;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\ItemResource\Pages;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive';

    protected static ?string $navigationGroup = 'Manage';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\BelongsToSelect::make('user_id')
                        ->relationship('user', 'name')
                        ->default(auth()->user()->id)
                        ->preload()
                        ->required()
                        ->searchable(),
                    Forms\Components\MarkdownEditor::make('content')
                        ->columnSpan(2)
                        ->required()
                        ->maxLength(65535),
                ])->columns()->columnSpan(3),

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
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('total_votes')->label('Votes')->sortable(),
                Tables\Columns\TextColumn::make('board.project.title'),
                Tables\Columns\TextColumn::make('board.title'),
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Date'),
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
