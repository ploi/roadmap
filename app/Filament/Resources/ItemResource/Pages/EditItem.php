<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Models\Board;
use App\Models\Project;
use App\Filament\Resources\ItemResource;
use App\Models\User;
use App\Notifications\Item\ItemUpdatedNotification;
use Filament\Resources\Pages\EditRecord;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;
}