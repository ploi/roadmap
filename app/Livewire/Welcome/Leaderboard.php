<?php

namespace App\Livewire\Welcome;

use App\Models\User;
use Livewire\Component;
use App\Settings\GeneralSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Leaderboard extends Component
{
    public string $activeTab = 'voters';

    public function mount(): void
    {
        $this->activeTab = 'voters';
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getTopVoters(): Collection
    {
        $limit = app(GeneralSettings::class)->leaderboard_users_count ?? 10;

        return User::query()
            ->select('users.*', DB::raw('COUNT(votes.id) as votes_count'))
            ->join('votes', 'users.id', '=', 'votes.user_id')
            ->where('users.hide_from_leaderboard', false)
            ->groupBy('users.id')
            ->orderByDesc('votes_count')
            ->limit($limit)
            ->get();
    }

    public function getTopCommenters(): Collection
    {
        $limit = app(GeneralSettings::class)->leaderboard_users_count ?? 10;

        return User::query()
            ->select('users.*', DB::raw('COUNT(comments.id) as comments_count'))
            ->join('comments', 'users.id', '=', 'comments.user_id')
            ->where('users.hide_from_leaderboard', false)
            ->groupBy('users.id')
            ->orderByDesc('comments_count')
            ->limit($limit)
            ->get();
    }

    public function render()
    {
        return view('livewire.welcome.leaderboard', [
            'topVoters' => $this->activeTab === 'voters' ? $this->getTopVoters() : collect(),
            'topCommenters' => $this->activeTab === 'commenters' ? $this->getTopCommenters() : collect(),
        ]);
    }
}
