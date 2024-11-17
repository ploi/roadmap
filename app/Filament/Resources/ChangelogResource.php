<?php

namespace App\Filament\Resources;

use App\Models\Item;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Changelog;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Settings\GeneralSettings;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\ChangelogResource\Pages;

class ChangelogResource extends Resource
{
    protected static ?string $model = Changelog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rss';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
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
                        ->default(auth()->user()?->id)
                        ->preload()
                        ->required()
                        ->searchable(),

                    DateTimePicker::make('published_at')
                        ->label(trans('resources.published-at')),

                    Select::make('related_items')
                        ->label(trans('resources.changelog.related-items'))
                        ->multiple()
                        ->preload()
                        ->relationship('items', 'title')
                        ->getOptionLabelFromRecordUsing(fn (Item $record) => $record->title . ($record->project ? ' (' . $record->project->title . ')' : '')),

                    MarkdownEditor::make('content')
                        ->label(trans('resources.changelog.content'))
                        ->columnSpan(2)
                        ->required()
                        ->minLength(5)
                        ->maxLength(65535),

                    ]
                )->columns(),
                ]
            );
    }

    /**
     * @throws \Exception
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
            ->actions(
                [
                Tables\Actions\EditAction::make(),
                ]
            )
            ->bulkActions(
                [
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListChangelogs::route('/'),
            'create' => Pages\CreateChangelog::route('/create'),
            'edit' => Pages\EditChangelog::route('/{record}/edit'),
        ];
    }
}
