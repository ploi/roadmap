<?php
/**
 * Class Vote
 *
 * This class represents a Livewire component for voting on a changelog.
 *
 * @since 2.13.0
 */

namespace App\Livewire\Changelog;

use Livewire\Component;
use App\Models\Changelog;
use Illuminate\Contracts\View\View;

class Vote extends Component
{
    public Changelog $changelog;

    /**
     * Whether to show the avatars of the most recent voters next to the button.
     */
    public bool $showVoters = false;

    public int $votersToShow = 5;

    /**
     * Toggles the vote for the authenticated user on the changelog, guests are sent to the login page.
     */
    public function vote(): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login');

            return;
        }

        $this->changelog->toggleUpvote(auth()->user());
    }

    /**
     * Render the component.
     *
     * The initial render uses the `votes_count` and `has_voted` attributes when the
     * parent eager loaded them, later requests query them because Livewire rehydrates a fresh model.
     */
    public function render(): View
    {
        $votesCount = $this->changelog->votes_count ?? $this->changelog->votes()->count();

        return view('livewire.changelog.vote', [
            'votesCount' => $votesCount,
            'hasVoted' => (bool) ($this->changelog->has_voted ?? $this->changelog->hasVoted()),
            'recentVoters' => $this->showVoters && $votesCount > 0
                ? $this->changelog->getRecentVoterDetails($this->votersToShow)
                : collect(),
        ]);
    }
}
