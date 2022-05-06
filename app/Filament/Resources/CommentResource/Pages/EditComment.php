<?php

namespace App\Filament\Resources\CommentResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CommentResource;

class EditComment extends EditRecord
{
    protected static string $resource = CommentResource::class;
}
