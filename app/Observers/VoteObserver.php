<?php

namespace App\Observers;

use App\Models\Vote;

class VoteObserver
{
    public function created(Vote $vote)
    {
        $this->updateTotalVotes($vote);
    }

    public function deleted(Vote $vote)
    {
        $this->updateTotalVotes($vote);
    }

    protected function updateTotalVotes(Vote $vote)
    {
        if (isset($vote->item->total_votes)) {
            $vote->item->total_votes = $vote->item->votes()->count();
            $vote->item->save();
        }
    }
}
