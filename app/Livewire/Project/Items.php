<?php

namespace App\Livewire\Project;

use App\Models\Item;
use App\Models\Board;
use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;

class Items extends Component
{
    public Project $project;
    public Board $board;

    /** @var Collection<int, Item> */
    public Collection $items;

    /**
     * @var string[]
     */
    protected $listeners = [
        'item-created' => '$refresh',
    ];

    public function render(): View
    {
        $this->items = $this->board->items()
            ->visibleForCurrentUser()
            ->latest($this->getSortingColumn())
            ->get()
            ->prioritize(function ($item) {
                return $item->isPinned();
            });

        return view('livewire.board.items');
    }

    protected function getSortingColumn(): string
    {
        return match ($this->board->sort_items_by) {
            'popular' => 'total_votes',
            default => 'created_at',
        };
    }
}
