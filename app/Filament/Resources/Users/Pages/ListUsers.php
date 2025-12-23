<?php

namespace App\Filament\Resources\Users\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Users\UserResource;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
}
