<?php

namespace App\Livewire\Changelog;

use Livewire\Component;
use App\Models\Changelog;
use AllowDynamicProperties;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;

#[AllowDynamicProperties]
class Vote extends Component
{
    public Changelog $changelog;

    public $votes;

    public function mount(Changelog $changelog): void
    {
        $this->changelog = $changelog;
        $this->votes = $changelog->votes;
    }

    public function vote(): void
    {
        $this->changelog->votes()->toggle(auth()->user());
        $this->votes = $this->changelog->votes;
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view(
            'livewire.changelog.vote',
            [
                'changelog' => $this->changelog,
                'votes' => $this->votes,
            ]
        );
    }
}
