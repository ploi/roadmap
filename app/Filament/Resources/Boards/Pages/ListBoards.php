<?php

namespace App\Filament\Resources\Boards\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Boards\BoardResource;

class ListBoards extends ListRecords
{
    protected static string $resource = BoardResource::class;
}
