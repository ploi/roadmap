<?php

namespace App\Filament\Resources\Votes\Pages;

use App\Filament\Resources\Votes\VoteResource;
use Filament\Resources\Pages\EditRecord;

class EditVote extends EditRecord
{
    protected static string $resource = VoteResource::class;
}
