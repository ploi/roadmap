<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProjectResource;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getTableReorderColumn(): ?string
    {
        return 'sort_order';
    }
}
