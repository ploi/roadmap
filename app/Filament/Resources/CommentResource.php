<?php

namespace App\Filament\Resources;

use App\Models\Comment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\CommentResource\Pages;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';

    protected static ?int $navigationSort = 200;

    public static function getNavigationGroup(): ?string
    {
        return trans('nav.content');
    }

    public static function getNavigationLabel(): string
    {
        return trans('nav.comment');
    }

    public static function getModelLabel(): string
    {
        return trans('resources.comment.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('resources.comment.label-plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                Section::make()
                    ->columns()
                    ->schema(
                        [
                           Select::make('user_id')
                               ->label(trans('resources.comment.user'))
                               ->columnSpan(1)
                               ->relationship('user', 'name')
                               ->searchable(),

                           Select::make('item_id')
                               ->label(trans('resources.comment.item'))
                               ->columnSpan(1)
                               ->relationship('item', 'title')
                               ->searchable(),

                           Toggle::make('private')
                               ->label(trans('resources.comment.private'))
                               ->helperText(trans('resources.comment.private-helper-text'))
                               ->label('Private')
                               ->default(false),

                           MarkdownEditor::make('content')
                               ->label(trans('resources.comment.content'))
                               ->columnSpan(2),
                           ]
                    )
                ]
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                TextColumn::make('content')
                    ->label(trans('resources.comment.content'))
                    ->wrap()
                    ->searchable(),

                TextColumn::make('item.title')
                    ->label(trans('resources.comment.item'))
                    ->wrap(),

                TextColumn::make('user.name')
                    ->label(trans('resources.comment.user')),

                TextColumn::make('created_at')
                    ->label(trans('resources.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->label('Date'),
                ]
            )
            ->filters(
                [
                //
                ]
            )
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
            'index'  => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit'   => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
