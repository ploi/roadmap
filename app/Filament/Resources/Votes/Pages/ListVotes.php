<?php

namespace App\Filament\Resources\Votes\Pages;

use App\Filament\Resources\Votes\VoteResource;
use Filament\Resources\Pages\ListRecords;

class ListVotes extends ListRecords
{
    protected static string $resource = VoteResource::class;
}
