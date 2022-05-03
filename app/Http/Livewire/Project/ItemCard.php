<?php

namespace App\Http\Livewire\Project;

use Livewire\Component;

class ItemCard extends Component
{
    public $projectItem;

    public $project;

    public function mount()
    {
        $this->project = $this->projectItem->board->project;
    }

    public function toggleUpvote()
    {
        $this->projectItem->toggleUpvote();
        $this->projectItem = $this->projectItem->refresh();
    }

    public function render()
    {
        return view('livewire.board.item-card');
    }
}