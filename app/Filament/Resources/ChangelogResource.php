<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Item;
use Filament\Tables;
use App\Models\Changelog;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ChangelogResource\Pages;

class ChangelogResource extends Resource
{
    protected static ?string $model = Changelog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rss';

    protected static ?string $navigationGroup = 'Manage';

    protected static ?string $navigationLabel = 'Changelog';

    protected static ?string $recordTitleAttribute = 'title';

    protected static function shouldRegisterNavigation(): bool
    {
        return app(GeneralSettings::class)->enable_changelog;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\TextInput::make('title')
                                              ->required()
                                              ->maxLength(255),
                    Forms\Components\Select::make('user_id')
                                           ->relationship('user', 'name')
                                           ->label('Author')
                                           ->default(auth()->user()->id)
                                           ->preload()
                                           ->required()
                                           ->searchable(),

                    Forms\Components\DateTimePicker::make('published_at'),
                    Forms\Components\MultiSelect::make('related_items')
                                                ->preload()->label('Related items')
                                                ->relationship('items', 'title')
                                                ->getOptionLabelFromRecordUsing(fn (Item $record) => $record->title . ($record->project ? ' (' . $record->project->title . ')' : '')),

                    Forms\Components\MarkdownEditor::make('content')
                                                   ->columnSpan(2)
                                                   ->required()
                                                   ->minLength(5)
                                                   ->maxLength(65535),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->wrap(),
                Tables\Columns\BooleanColumn::make('published')
                    ->getStateUsing(fn ($record) => filled($record->published_at) && now()->greaterThanOrEqualTo($record->published_at)),
                Tables\Columns\TextColumn::make('published_at')->dateTime(Auth()->user()->date_time_format)->since()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(Auth()->user()->date_time_format)->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_published')
                    ->query(fn (Builder $query): Builder => $query->where('published_at', '<=', now()))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListChangelogs::route('/'),
            'create' => Pages\CreateChangelog::route('/create'),
            'edit' => Pages\EditChangelog::route('/{record}/edit'),
        ];
    }
}
