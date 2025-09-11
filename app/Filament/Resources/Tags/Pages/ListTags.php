<?php

namespace App\Filament\Resources\Tags\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Tags\TagResource;
use Filament\Resources\Pages\ListRecords;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
