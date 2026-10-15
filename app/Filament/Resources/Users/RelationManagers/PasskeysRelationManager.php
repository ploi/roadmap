<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Tables\Table;
use Laravel\Passkeys\Passkey;
use App\Settings\GeneralSettings;
use Filament\Actions\DeleteAction;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passkeys\Actions\DeletePasskey;
use Filament\Resources\RelationManagers\RelationManager;

/**
 * Lists a user's passkeys so an admin can revoke one, for example when a device got lost.
 */
class PasskeysRelationManager extends RelationManager
{
    protected static string $relationship = 'passkeys';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('resources.passkey.label-plural');
    }

    /**
     * Stays visible for users that still have passkeys after passkeys got disabled, so they can be revoked.
     */
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return app(GeneralSettings::class)->enable_passkeys || $ownerRecord->passkeys()->exists();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                TextColumn::make('name')
                    ->label(trans('resources.passkey.name')),

                TextColumn::make('authenticator')
                    ->label(trans('resources.passkey.authenticator'))
                    ->placeholder('-'),

                TextColumn::make('last_used_at')
                    ->label(trans('resources.passkey.last-used-at'))
                    ->dateTime()
                    ->placeholder(trans('resources.passkey.never-used')),

                TextColumn::make('created_at')
                    ->label(trans('resources.passkey.created-at'))
                    ->dateTime(),
                ]
            )
            ->recordActions(
                [
                DeleteAction::make()
                    ->label(trans('resources.passkey.revoke'))
                    ->modalAlignment(Alignment::Left)
                    ->using(fn (Passkey $record) => app(DeletePasskey::class)($record->user, $record)),
                ]
            )
            ->defaultSort('created_at', 'desc');
    }
}
