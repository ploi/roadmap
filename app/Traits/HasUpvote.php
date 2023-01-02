<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Vote;
use Livewire\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasUpvote
{
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'model');
    }

    public function hasVoted(User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return false;
        }

        return (bool)$this->votes()->where('user_id', $user->id)->exists();
    }

    public function toggleUpvote(User $user = null): Vote|Model|RedirectResponse|bool|Redirector
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $vote = $this->votes()->where('user_id', $user->id)->first();

        if ($vote) {
            $vote->delete();

            return true;
        }

        $vote = $this->votes()->create();
        $vote->user()->associate($user)->save();

        return $vote;
    }

    public function getUserVote(User $user = null): Vote|null
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return null;
        }

        return $this->votes()->where('user_id', $user->id)->first();
    }



    /**
     *  Returns a collection of the most recent users who have voted for this item.
     *
     * @param int $count Displays five users by default.
     * @return Collection|\Illuminate\Support\Collection
     */
    public function getRecentVoterDetails(int $count = 5): Collection|\Illuminate\Support\Collection
    {
        return $this->votes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take($count)
            ->get()
            ->map(function ($vote) {
                return [
                    'name' => $vote->user->name,
                    'avatar' => $vote->user->getGravatar('50'),
                ];
            });
    }
}
