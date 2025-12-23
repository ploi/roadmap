<?php

namespace App\Filament\Resources\Votes\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Votes\VoteResource;

class CreateVote extends CreateRecord
{
    protected static string $resource = VoteResource::class;
}
