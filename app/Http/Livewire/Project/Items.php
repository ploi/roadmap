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
        $this->items = $this->board->items()
            ->latest($this->getSortingColumn())
            ->get()
            ->prioritize(function ($item) {
                return $item->isPinned();
            });

        return view('livewire.board.items');
    }

    protected function getSortingColumn()
    {
        return match ($this->board->sort_items_by) {
            'popular' => 'total_votes',
            'latest' => 'created_at',
        };
    }
}
