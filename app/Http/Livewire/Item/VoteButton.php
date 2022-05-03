<?php

namespace App\Http\Livewire\Item;

use Livewire\Component;

class VoteButton extends Component
{
    public $item;

    public function toggleUpvote()
    {
        $this->item->toggleUpvote();
        $this->item = $this->item->refresh();
    }

    public function render()
    {
        return view('livewire.item.vote-button');
    }
}