<?php

namespace App\Filament\Resources\Boards\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Boards\BoardResource;

class CreateBoard extends CreateRecord
{
    protected static string $resource = BoardResource::class;
}
