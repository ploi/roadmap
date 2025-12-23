<?php

namespace App\Filament\Resources\Inboxes\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Inboxes\InboxResource;

class CreateInbox extends CreateRecord
{
    protected static string $resource = InboxResource::class;
}
