<?php

namespace App\Filament\Resources;

use Spatie\Tags\Tag;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use App\Filament\Resources\TagResource\Pages;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables\Actions\DeleteBulkAction;

class TagResource extends Resource
{
    use Translatable;

    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 1200;

	public static function getNavigationGroup(): ?string {
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                       ->schema([
                           TextInput::make('name')
                                    ->label(trans('resources.tag.name'))
                                    ->required(),

                           Checkbox::make('changelog')
                                   ->label(trans('resources.tag.changelog')),
                       ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                          ->label(trans('resources.tag.name'))
                          ->searchable(),
                TextColumn::make('created_at')
                          ->label(trans('resources.created-at'))
                          ->sortable()
                          ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
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
            'index'  => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit'   => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
