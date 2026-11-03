<?php

namespace App\Filament\Resources\Webhooks;

use Illuminate\Support\Str;
use App\Enums\WebhookEvent;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\WebhookEndpoint;
use App\Rules\SafeWebhookUrl;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\Webhooks\Pages\EditWebhook;
use App\Filament\Resources\Webhooks\Pages\ListWebhooks;
use App\Filament\Resources\Webhooks\Pages\CreateWebhook;

class WebhookResource extends Resource
{
    protected static ?string $model = WebhookEndpoint::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bolt';

    protected static ?int $navigationSort = 1300;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function getNavigationGroup(): ?string
    {
        return trans('nav.manage');
    }

    public static function getNavigationLabel(): string
    {
        return trans('nav.webhooks');
    }

    public static function getModelLabel(): string
    {
        return trans('resources.webhook.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('resources.webhook.label-plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('url')
                            ->label(trans('resources.webhook.url'))
                            ->url()
                            ->rule(new SafeWebhookUrl())
                            ->maxLength(2048)
                            ->required(),

                        TextInput::make('secret')
                            ->label(trans('resources.webhook.secret'))
                            ->helperText(trans('resources.webhook.secret-helper-text'))
                            ->password()
                            ->revealable()
                            ->default(fn () => Str::random(40))
                            ->required(),

                        CheckboxList::make('events')
                            ->label(trans('resources.webhook.events'))
                            ->options(WebhookEvent::options())
                            ->required(),

                        Toggle::make('active')
                            ->label(trans('resources.webhook.active'))
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')
                    ->label(trans('resources.webhook.url'))
                    ->searchable(),
                TextColumn::make('events')
                    ->label(trans('resources.webhook.events'))
                    ->badge(),
                IconColumn::make('active')
                    ->label(trans('resources.webhook.active'))
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWebhooks::route('/'),
            'create' => CreateWebhook::route('/create'),
            'edit' => EditWebhook::route('/{record}/edit'),
        ];
    }
}
