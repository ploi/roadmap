<?php

namespace App\Livewire\Welcome;

use App\Models\Item;
use App\Models\User;
use App\Models\Vote;
use App\Models\Comment;
use Livewire\Component;

class Statistics extends Component
{
    public function getStatistics(): array
    {
        return [
            [
                'label' => trans('general.items'),
                'value' => Item::count(),
                'icon' => 'heroicon-o-document-text',
                'color' => 'text-green-600 dark:text-green-400',
                'bg_color' => 'bg-green-50 dark:bg-green-900/20',
            ],
            [
                'label' => trans('general.comments'),
                'value' => Comment::count(),
                'icon' => 'heroicon-o-chat-bubble-left-right',
                'color' => 'text-blue-600 dark:text-blue-400',
                'bg_color' => 'bg-blue-50 dark:bg-blue-900/20',
            ],
            [
                'label' => trans('general.votes'),
                'value' => Vote::count(),
                'icon' => 'heroicon-o-hand-thumb-up',
                'color' => 'text-yellow-600 dark:text-yellow-400',
                'bg_color' => 'bg-yellow-50 dark:bg-yellow-900/20',
            ],
        ];
    }

    public function getRecentActivity(): array
    {
        $now = now();
        $weekAgo = $now->copy()->subWeek();

        return [
            'new_items' => Item::where('created_at', '>=', $weekAgo)->count(),
            'new_comments' => Comment::where('created_at', '>=', $weekAgo)->count(),
            'new_votes' => Vote::where('created_at', '>=', $weekAgo)->count(),
            'new_users' => User::where('created_at', '>=', $weekAgo)->count(),
        ];
    }

    public function render()
    {
        return view('livewire.welcome.statistics', [
            'statistics' => $this->getStatistics(),
            'recentActivity' => $this->getRecentActivity(),
        ]);
    }
}
