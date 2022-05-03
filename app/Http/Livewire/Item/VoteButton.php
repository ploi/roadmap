<?php

namespace App\Http\Livewire\Item;

use App\Models\Item;
use Livewire\Component;

class VoteButton extends Component
{
    public Item $item;
    public bool $hasVoted = false;

    public function toggleUpvote()
    {
        $this->item->toggleUpvote();
        $this->item = $this->item->refresh();
    }

    public function render()
    {
        $this->hasVoted = $this->item->hasVoted();

        return view('livewire.item.vote-button');
    }
}
