<?php

namespace App\Filament\Resources\Comments\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Comments\CommentResource;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;
}
