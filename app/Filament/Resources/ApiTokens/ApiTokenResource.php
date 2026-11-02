<?php

namespace App\Filament\Resources\ApiTokens;

use App\Models\ApiToken;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\ApiTokens\Pages\ListApiTokens;
use App\Filament\Resources\ApiTokens\Pages\CreateApiToken;

class ApiTokenResource extends Resource
{
    protected static ?string $model = ApiToken::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-key';

    protected static ?int $navigationSort = 1400;

    public static function getNavigationGroup(): ?string
    {
        return trans('nav.manage');
    }

    public static function getNavigationLabel(): string
    {
        return trans('nav.api-tokens');
    }

    public static function getModelLabel(): string
    {
        return trans('resources.api-token.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('resources.api-token.label-plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(trans('resources.api-token.name'))
                ->maxLength(255)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(trans('resources.api-token.name'))
                    ->searchable(),
                TextColumn::make('last_used_at')
                    ->label(trans('resources.api-token.last-used-at'))
                    ->since()
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label(trans('resources.created-at'))
                    ->dateTime(),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiTokens::route('/'),
            'create' => CreateApiToken::route('/create'),
        ];
    }
}
