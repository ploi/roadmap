<?php

namespace App\Http\Livewire\Item;

use App\Models\Item;
use App\Models\Vote;
use Illuminate\Support\Collection;
use Livewire\Component;

class VoteButton extends Component
{
    public Item $item;
    public Vote|null $vote;
    public Collection $recentVoters;
    public int $recentVotersToShow = 5;

    public function toggleUpvote()
    {
        $this->item->toggleUpvote();
        $this->item = $this->item->refresh();
    }

    public function unsubscribe()
    {
        $this->vote->update(['subscribed' => false]);

        $this->item = $this->item->refresh();
    }

    public function subscribe()
    {
        $this->vote->update(['subscribed' => true]);

        $this->item = $this->item->refresh();
    }

    public function render()
    {
        $this->vote = $this->item->getUserVote();

        $this->recentVoters = $this->item->getRecentVoterDetails($this->recentVotersToShow);

        return view('livewire.item.vote-button');
    }
}
