<?php

namespace App\Filament\Resources\Boards\Pages;

use App\Filament\Resources\Boards\BoardResource;
use Filament\Resources\Pages\ListRecords;

class ListBoards extends ListRecords
{
    protected static string $resource = BoardResource::class;
}
