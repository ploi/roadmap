<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\User;
use App\Models\Vote;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make(__('Users'), User::count()),
            Card::make(__('Votes'), Vote::count()),
            Card::make('Items', Item::count()),
        ];
    }
}
