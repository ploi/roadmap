<?php

namespace App\Filament\Resources\Comments\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Comments\CommentResource;

class CreateComment extends CreateRecord
{
    protected static string $resource = CommentResource::class;
}
