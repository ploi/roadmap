<?php

namespace App\Filament\Resources\Tags\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Tags\TagResource;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateTag extends CreateRecord
{
    use Translatable;

    protected static string $resource = TagResource::class;
}
