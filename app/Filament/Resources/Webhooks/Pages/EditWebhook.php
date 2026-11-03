<?php

namespace App\Filament\Resources\Webhooks\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Webhooks\WebhookResource;

class EditWebhook extends EditRecord
{
    protected static string $resource = WebhookResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // `secret` is hidden on the model, so Filament would otherwise leave the field empty.
        $data['secret'] = $this->record->secret;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
