<?php

namespace App\Filament\Resources\Changelogs;

use App\Filament\Resources\Changelogs\RelationManagers\ItemsRelationManager;
use Exception;
use App\Models\Item;
use App\Models\Changelog;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use App\Settings\GeneralSettings;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\Changelogs\Pages\EditChangelog;
use App\Filament\Resources\Changelogs\Pages\ListChangelogs;
use App\Filament\Resources\Changelogs\Pages\CreateChangelog;

class ChangelogResource extends Resource
{
    protected static ?string $model = Changelog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rss';

    protected static ?int $navigationSort = 400;

    protected static ?string $navigationLabel = 'Changelog';

    protected static ?string $recordTitleAttribute = 'title';


    public static function getNavigationGroup(): ?string
    {
        return trans('nav.content');
    }

    public static function getNavigationLabel(): string
    {
        return trans('nav.changelog');
    }

    public static function getModelLabel(): string
    {
        return trans('resources.changelog.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('resources.changelog.label-plural');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return app(GeneralSettings::class)->enable_changelog;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                [
                Section::make(
                    [
                    TextInput::make('title')
                        ->label(trans('resources.changelog.title'))
                        ->required()
                        ->maxLength(255),

                    Select::make('user_id')
                        ->label(trans('resources.changelog.author'))
                        ->relationship('user', 'name')
                        ->default(auth()->user()->id)
                        ->preload()
                        ->required()
                        ->searchable(),

                    DateTimePicker::make('published_at')
                        ->label(trans('resources.published-at')),

                    MarkdownEditor::make('content')
                        ->label(trans('resources.changelog.content'))
                        ->columnSpan(2)
                        ->required()
                        ->minLength(5)
                        ->maxLength(65535),

                    ]
                )->columns()->columnSpanFull(),
                ]
            );
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                TextColumn::make('title')
                    ->label(trans('resources.changelog.title'))
                    ->searchable()
                    ->wrap(),

                IconColumn::make('published')
                    ->label(trans('resources.changelog.published'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->published_at) && now()->greaterThanOrEqualTo($record->published_at)),

                TextColumn::make('published_at')
                    ->label(trans('resources.published-at'))
                    ->dateTime()
                    ->since()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(trans('resources.created-at'))
                    ->dateTime()
                    ->sortable(),
                ]
            )
            ->filters(
                [
                Filter::make('is_published')
                    ->label(trans('resources.changelog.is-published'))
                    ->query(fn (Builder $query): Builder => $query->where('published_at', '<=', now()))
                ]
            )
            ->recordActions(
                [
                EditAction::make(),
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
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChangelogs::route('/'),
            'create' => CreateChangelog::route('/create'),
            'edit' => EditChangelog::route('/{record}/edit'),
        ];
    }
}
