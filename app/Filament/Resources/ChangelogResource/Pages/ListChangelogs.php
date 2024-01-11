<?php

namespace App\Filament\Resources\ChangelogResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ChangelogResource;

class ListChangelogs extends ListRecords
{
    protected static string $resource = ChangelogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
