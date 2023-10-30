<?php

namespace App\Livewire\Item;

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

    public function mount(bool $hideSubscribeOption = false)
    {
        $this->showSubscribeOption = ! $hideSubscribeOption;
    }

    public function toggleUpvote()
    {
        $this->model->toggleUpvote();
        $this->model = $this->model->refresh();
    }

    public function unsubscribe()
    {
        $this->vote->update(['subscribed' => false]);

        $this->model = $this->model->refresh();
    }

    public function subscribe()
    {
        $this->vote->update(['subscribed' => true]);

        $this->model = $this->model->refresh();
    }

    public function render(): View
    {
        $this->vote = $this->model->getUserVote();

        $this->recentVoters = $this->model->getRecentVoterDetails($this->recentVotersToShow);

        return view('livewire.item.vote-button');
    }
}
