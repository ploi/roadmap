<?php

namespace App\Filament\Resources\Changelogs\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Changelogs\ChangelogResource;

class CreateChangelog extends CreateRecord
{
    protected static string $resource = ChangelogResource::class;
}
