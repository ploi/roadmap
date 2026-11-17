<?php

namespace App\Filament\Resources\ApiTokens\Pages;

use App\Models\ApiToken;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ApiTokens\ApiTokenResource;

class CreateApiToken extends CreateRecord
{
    protected static string $resource = ApiTokenResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        [$token, $plainTextToken] = ApiToken::generate($data['name']);

        Notification::make()
            ->title(trans('resources.api-token.created-title'))
            ->body(trans('resources.api-token.created-body', ['token' => $plainTextToken]))
            ->success()
            ->persistent()
            ->send();

        return $token;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
