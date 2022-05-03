<?php

namespace App\Http\Livewire\Project;

use App\Models\Item;
use Livewire\Component;

class ItemCard extends Component
{
    public Item $item;

    public $project;

    public function mount()
    {
        $this->project = $this->item->board->project;
    }

    public function toggleUpvote()
    {
        $this->item->toggleUpvote();
        $this->item = $this->item->refresh();
    }

    public function render()
    {
        return view('livewire.board.item-card');
    }
}
