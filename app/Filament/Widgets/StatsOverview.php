<?php

namespace App\Filament\Widgets;

use App\Models\Board;
use App\Models\Comment;
use App\Models\Item;
use App\Models\Project;
use App\Models\User;
use App\Models\Vote;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(trans('widgets.items'), Item::count())
                ->description(trans('widgets.total-items-description'))
                ->descriptionIcon('heroicon-m-document-text')
                ->chart($this->getItemsTrend())
                ->color('success'),

            Stat::make(trans('widgets.comments'), Comment::count())
                ->description(trans('widgets.total-comments-description'))
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->chart($this->getCommentsTrend())
                ->color('info'),

            Stat::make(trans('widgets.votes'), Vote::count())
                ->description(trans('widgets.total-votes-description'))
                ->descriptionIcon('heroicon-m-hand-thumb-up')
                ->chart($this->getVotesTrend())
                ->color('warning'),

            Stat::make(trans('widgets.users'), User::count())
                ->description(trans('widgets.total-users-description'))
                ->descriptionIcon('heroicon-m-user-group')
                ->chart($this->getUsersTrend())
                ->color('primary'),

            Stat::make(trans('widgets.projects'), Project::count())
                ->description(trans('widgets.total-projects-description'))
                ->descriptionIcon('heroicon-m-folder')
                ->color('secondary'),

            Stat::make(trans('widgets.boards'), Board::count())
                ->description(trans('widgets.total-boards-description'))
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('gray'),
        ];
    }

    protected function getItemsTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $count = Item::whereDate('created_at', now()->subDays($i)->toDateString())->count();
            $data[] = $count;
        }
        return $data;
    }

    protected function getCommentsTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $count = Comment::whereDate('created_at', now()->subDays($i)->toDateString())->count();
            $data[] = $count;
        }
        return $data;
    }

    protected function getVotesTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $count = Vote::whereDate('created_at', now()->subDays($i)->toDateString())->count();
            $data[] = $count;
        }
        return $data;
    }

    protected function getUsersTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $count = User::whereDate('created_at', now()->subDays($i)->toDateString())->count();
            $data[] = $count;
        }
        return $data;
    }
}
