<?php

namespace App\Filament\Resources\CommentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CommentResource;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;
}
