<?php

namespace App\Livewire\Project;

use App\Models\Board;
use App\Models\Project;
use Livewire\Component;

class BoardColumn extends Component
{
    public Project $project;
    public Board $board;
    public string $sortBy = 'created_at';
    public string $search = '';

    protected $listeners = [
        'item-created' => '$refresh',
    ];

    public function mount(): void
    {
        $this->sortBy = 'created_at';
    }

    public function setSortBy(string $sort): void
    {
        if (! in_array($sort, ['created_at', 'total_votes', 'last_commented'])) {
            return;
        }

        $this->sortBy = $sort;
    }

    public function render()
    {
        $query = $this->board->items()
            ->visibleForCurrentUser()
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%' . $this->search . '%'));

        if ($this->sortBy === 'last_commented') {
            $query->withMax('comments', 'created_at')
                ->orderBy('pinned', 'desc')
                ->orderByDesc('comments_max_created_at');
        } else {
            $query->orderBy('pinned', 'desc')
                ->orderByDesc($this->sortBy);
        }

        $items = $query->get();

        return view('livewire.project.board-column', [
            'items' => $items,
        ]);
    }
}
