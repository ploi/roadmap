<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Project;
use Livewire\Component;
use Livewire\Attributes\On;

class SpotlightSearch extends Component
{
    public string $query = '';
    public bool $isOpen = false;
    public array $items = [];
    public array $projects = [];
    public int $totalResults = 0;

    #[On('open-spotlight')]
    public function open(): void
    {
        $this->isOpen = true;
        $this->query = '';
        $this->items = [];
        $this->projects = [];
        $this->totalResults = 0;

        // Dispatch browser event for Alpine.js
        $this->dispatch('spotlight-opened');
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->query = '';
        $this->items = [];
        $this->projects = [];
        $this->totalResults = 0;
    }

    public function updatedQuery(): void
    {
        if (empty(trim($this->query))) {
            $this->items = [];
            $this->projects = [];
            $this->totalResults = 0;
            return;
        }

        $this->searchItems();
        $this->searchProjects();
        $this->totalResults = count($this->items) + count($this->projects);
    }

    protected function searchItems(): void
    {
        $query = Item::query()
            ->visibleForCurrentUser()
            ->with(['project', 'board', 'user'])
            ->where(function ($q) {
                $q->where('title', 'like', '%' . $this->query . '%')
                    ->orWhere('content', 'like', '%' . $this->query . '%');
            })
            ->orderByDesc('created_at')
            ->limit(8);

        $this->items = $query->get()->map(function (Item $item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'project_title' => $item->project?->title,
                'board_title' => $item->board?->title,
                'votes_count' => $item->total_votes ?? 0,
                'created_at' => $item->created_at?->diffForHumans(),
                'url' => route('items.show', $item),
            ];
        })->toArray();
    }

    protected function searchProjects(): void
    {
        $query = Project::query()
            ->visibleForCurrentUser()
            ->with(['boards'])
            ->where(function ($q) {
                $q->where('title', 'like', '%' . $this->query . '%')
                    ->orWhere('description', 'like', '%' . $this->query . '%');
            })
            ->orderBy('title')
            ->limit(8);

        $this->projects = $query->get()->map(function (Project $project) {
            return [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'description' => $project->description,
                'icon' => $project->icon,
                'boards_count' => $project->boards->count(),
                'url' => route('projects.show', $project),
            ];
        })->toArray();
    }

    public function createNewItem(): void
    {
        $query = $this->query;
        $this->close();
        $this->dispatch('create-item-from-search', query: $query);
    }

    public function render()
    {
        return view('livewire.spotlight-search');
    }
}
