<?php

namespace App\Filament\Resources\VoteResource\Pages;

use App\Filament\Resources\VoteResource;
use Filament\Resources\Pages\ListRecords;

class ListVotes extends ListRecords
{
    protected static string $resource = VoteResource::class;
}
