<?php

namespace App\Filament\Resources\Tags;

use Spatie\Tags\Tag;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use App\Filament\Resources\Tags\Pages\EditTag;
use App\Filament\Resources\Tags\Pages\ListTags;
use App\Filament\Resources\Tags\Pages\CreateTag;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class TagResource extends Resource
{
    use Translatable;

    protected static ?string $model = Tag::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 1200;

    public static function getNavigationGroup(): ?string
    {
        return trans('nav.manage');
    }

    public static function getNavigationLabel(): string
    {
        return trans('nav.tags');
    }

    public static function getModelLabel(): string
    {
        return trans('resources.tag.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('resources.tag.label-plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                [
                Section::make()
                    ->columnSpanFull()
                    ->schema(
                        [
                           TextInput::make('name')
                               ->label(trans('resources.tag.name'))
                               ->required(),

                           Checkbox::make('changelog')
                               ->label(trans('resources.tag.changelog')),
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
                TextColumn::make('name')
                    ->label(trans('resources.tag.name'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(trans('resources.created-at'))
                    ->sortable()
                    ->dateTime(),
                ]
            )
            ->filters(
                [
                //
                ]
            )
            ->recordActions(
                [
                EditAction::make(),
                DeleteAction::make(),
                ]
            )
            ->toolbarActions(
                [
                DeleteBulkAction::make(),
                ]
            );
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
            'index'  => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit'   => EditTag::route('/{record}/edit'),
        ];
    }
}
