<?php

namespace App\Filament\Resources\Votes\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Votes\VoteResource;

class EditVote extends EditRecord
{
    protected static string $resource = VoteResource::class;
}
