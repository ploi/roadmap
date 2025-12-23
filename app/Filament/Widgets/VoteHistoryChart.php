<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;

class VoteHistoryChart extends ChartWidget
{
    protected ?string $pollingInterval = null;

    public ?Item $item = null;

    public ?string $filter = 'all';

    public function getHeading(): ?string
    {
        return trans('items.vote-history');
    }

    public function getDescription(): ?string
    {
        return trans('items.vote-history-description');
    }

    protected function getFilters(): ?array
    {
        return [
            'all' => trans('items.filter-all-time'),
            '7' => trans('items.filter-last-7-days'),
            '30' => trans('items.filter-last-30-days'),
            '90' => trans('items.filter-last-90-days'),
            '365' => trans('items.filter-last-year'),
            '730' => trans('items.filter-last-2-years'),
            '1095' => trans('items.filter-last-3-years'),
        ];
    }

    protected function getData(): array
    {
        if (! $this->item) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $query = $this->item->votes();

        if ($this->filter && $this->filter !== 'all') {
            $days = (int) $this->filter;
            $query->where('created_at', '>=', Carbon::now()->subDays($days));
        }

        $votes = $query
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();

        if ($votes->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $startDate = Carbon::parse($votes->first()->date);
        $endDate = Carbon::parse($votes->last()->date);

        $labels = [];
        $data = [];
        $votesMap = $votes->pluck('count', 'date')->toArray();

        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('M d, Y');
            $data[] = $votesMap[$dateKey] ?? 0;
            $currentDate->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => trans('items.votes-per-day'),
                    'data' => $data,
                    'borderColor' => 'rgb(99, 102, 241)',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
