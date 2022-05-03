<?php

namespace App\Observers;

use App\Models\Vote;

class VoteObserver
{
    public function created(Vote $vote)
    {
        $vote->item->increment('total_votes');
    }

    public function deleted(Vote $vote)
    {
        $vote->item->decrement('total_votes');
    }
}