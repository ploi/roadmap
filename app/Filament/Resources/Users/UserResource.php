<?php

namespace App\Filament\Resources\Users;

use App\Models\User;
use App\Enums\UserRole;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use App\Filament\Resources\Users\Pages\EditUser;
use STS\FilamentImpersonate\Actions\Impersonate;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\Users\RelationManagers\VotesRelationManager;
use App\Filament\Resources\Users\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\Users\RelationManagers\ProjectsRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1000;

    public static function getNavigationGroup(): ?string
    {
        return trans('nav.manage');
    }

    public static function getNavigationLabel(): string
    {
        return trans('nav.users');
    }

    public static function getModelLabel(): string
    {
        return trans('resources.user.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('resources.user.label-plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                [
                    Section::make(
                        [

                            TextInput::make('name')
                                ->label(trans('resources.user.name'))
                                ->required(),

                            TextInput::make('email')
                                ->label(trans('resources.user.email'))
                                ->email()
                                ->required(),

                            Select::make('role')
                                ->label(trans('resources.user.role'))
                                ->required()
                                ->options(
                                    [
                                        UserRole::User->value => trans('resources.user.roles.user'),
                                        UserRole::Employee->value => trans('resources.user.roles.employee'),
                                        UserRole::Admin->value => trans('resources.user.roles.admin'),
                                    ]
                                )

                        ]
                    )->columns()
                    ->columnSpanFull()
                ]
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                    TextColumn::make('name')
                        ->label(trans('resources.user.name'))
                        ->searchable(),

                    TextColumn::make('email')
                        ->label(trans('resources.user.email'))
                        ->searchable(),

                    TextColumn::make('role')
                        ->label(trans('resources.user.role'))
                        ->formatStateUsing(fn ($state) => trans("resources.user.roles.{$state->value}")),

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
                    Impersonate::make()
                ]
            )
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            CommentsRelationManager::class,
            VotesRelationManager::class,
            ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
