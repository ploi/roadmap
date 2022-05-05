<?php

namespace App\Http\Livewire\Project;

use App\Models\Board;
use App\Models\Project;
use Livewire\Component;

class Items extends Component
{
    public Project $project;
    public Board $board;

    public $items = [];

    protected $listeners = [
        'item-created' => '$refresh',
    ];

    public function render()
    {
        $this->items = $this->board->items()->latest()->get();

        return view('livewire.board.items');
    }
}
