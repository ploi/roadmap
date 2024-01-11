<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Enums\UserRole;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\UserResource\Pages;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
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
            ->actions(
                [
                Impersonate::make()
                ]
            )
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\VotesRelationManager::class,
            RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
