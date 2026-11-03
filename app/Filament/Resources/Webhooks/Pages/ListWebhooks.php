<?php

namespace App\Filament\Resources\Webhooks\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Webhooks\WebhookResource;

class ListWebhooks extends ListRecords
{
    protected static string $resource = WebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
