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
use AllowDynamicProperties;
use Illuminate\Contracts\View\View;

/**
 * Class Vote
 *
 */
#[AllowDynamicProperties]
class Vote extends Component
{
    public Changelog $changelog;

    public $votes;

    /**
     * Mount the component.
     *
     * @param  Changelog  $changelog  The Changelog instance to be mounted.
     *
     * @return void
     */
    public function mount(Changelog $changelog): void
    {
        $this->changelog = $changelog;
        $this->votes = $changelog->votes;
    }

    /**
     * Toggles the vote for the authenticated user on the changelog.
     * Updates the votes count on the changelog.
     *
     * @return void
     */
    public function vote(): void
    {
        $this->changelog->votes()->toggle(auth()->user());
        $this->votes = $this->changelog->votes;
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
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
