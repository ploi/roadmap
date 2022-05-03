<?php

namespace App\Http\Livewire\Project;

use Livewire\Component;

class Items extends Component
{
    public $project;

    public $projectItems = [];

    protected $listeners = [
        'item-created' => '$refresh',
    ];

    public function render()
    {
        $this->projectItems = $this->project->items()->orderBy('created_at', 'desc')->get();

        return view('livewire.board.items');
    }
}