<?php

namespace App\Livewire\Item;

use App\Models\Item;
use App\Models\Vote;
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class VoteButton extends Component
{
    public Model $model;
    public Vote|null $vote;
    public Collection $recentVoters;
    public int $recentVotersToShow = 5;
    public bool $showSubscribeOption;
    public bool $compact = false;

    public function mount(bool $hideSubscribeOption = false)
    {
        $this->showSubscribeOption = ! $hideSubscribeOption;
    }

    public function toggleUpvote()
    {
        abort_if($this->model instanceof Item && $this->model->board?->block_votes, 403);

        $this->model->toggleUpvote();
        $this->refreshModel();
    }

    public function unsubscribe()
    {
        $this->vote->update(['subscribed' => false]);

        $this->refreshModel();
    }

    public function subscribe()
    {
        $this->vote->update(['subscribed' => true]);

        $this->refreshModel();
    }

    /**
     * Comments come with their votes (and voters) eager loaded; keep it that way after a change.
     */
    private function refreshModel(): void
    {
        $this->model = $this->model->refresh();

        if ($this->model->relationLoaded('votes')) {
            $this->model->loadMissing('votes.user');
        }
    }

    public function render(): View
    {
        $this->vote = $this->model->getUserVote();

        $this->recentVoters = $this->model->getRecentVoterDetails($this->recentVotersToShow);

        return view('livewire.item.vote-button');
    }
}
