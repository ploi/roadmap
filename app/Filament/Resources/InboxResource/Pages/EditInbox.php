<?php

namespace App\Filament\Resources\InboxResource\Pages;

use App\Filament\Resources\ItemResource\Pages\EditItem;
use App\Models\Board;
use App\Models\Project;
use App\Filament\Resources\InboxResource;

class EditInbox extends EditItem
{
    protected static string $resource = InboxResource::class;
}
