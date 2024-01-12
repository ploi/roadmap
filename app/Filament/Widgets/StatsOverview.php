<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\User;
use App\Models\Vote;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Stat::make(trans('widgets.users'), User::count()),
            Stat::make(trans('widgets.votes'), Vote::count()),
            Stat::make(trans('widgets.items'), Item::count()),
        ];
    }
}
