<?php

namespace App\Observers;

use App\Models\Vote;

class VoteObserver
{
    public function created(Vote $vote): void
    {
        $this->updateTotalVotes($vote);
    }

    public function deleted(Vote $vote): void
    {
        $this->updateTotalVotes($vote);
    }

    protected function updateTotalVotes(Vote $vote): void
    {
        if (isset($vote->item->total_votes)) {
            $vote->item->total_votes = $vote->item->votes()->count();
            $vote->item->save();
        }
    }
}
