<?php

namespace App\Filament\Resources\Projects\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Projects\ProjectResource;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;
}
