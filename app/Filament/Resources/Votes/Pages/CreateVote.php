<?php

namespace App\Filament\Resources\Votes\Pages;

use App\Filament\Resources\Votes\VoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVote extends CreateRecord
{
    protected static string $resource = VoteResource::class;
}
