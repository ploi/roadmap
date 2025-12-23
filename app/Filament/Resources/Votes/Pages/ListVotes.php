<?php

namespace App\Filament\Resources\Votes\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Votes\VoteResource;

class ListVotes extends ListRecords
{
    protected static string $resource = VoteResource::class;
}
