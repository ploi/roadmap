<?php

namespace App\Filament\Resources\Tags\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use App\Filament\Resources\Tags\TagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    use Translatable;

    protected static string $resource = TagResource::class;
}
