<?php

namespace App\Filament\Resources\Webhooks\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Webhooks\WebhookResource;

class CreateWebhook extends CreateRecord
{
    protected static string $resource = WebhookResource::class;
}
