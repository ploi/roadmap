<?php

namespace App\Filament\Resources\Comments;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Comments\Pages\ListComments;
use App\Filament\Resources\Comments\Pages\CreateComment;
use App\Filament\Resources\Comments\Pages\EditComment;
use App\Models\Comment;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\CommentResource\Pages;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns()
                    ->columnSpanFull()
                    ->schema([
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
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->sortable(),
            ])
            ->recordActions([
                DeleteAction::make()->modalAlignment(Alignment::Left)
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
            'index' => ListComments::route('/'),
            'create' => CreateComment::route('/create'),
            'edit' => EditComment::route('/{record}/edit'),
        ];
    }
}
