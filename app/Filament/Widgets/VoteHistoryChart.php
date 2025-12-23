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

        $days = $this->filter && $this->filter !== 'all' ? (int) $this->filter : null;

        if ($days) {
            $query->where('created_at', '>=', Carbon::now()->subDays($days));
        }

        // Determine grouping based on time range
        $groupBy = $this->getGroupingStrategy($days);

        if ($groupBy === 'month') {
            return $this->getMonthlyData($query);
        } elseif ($groupBy === 'week') {
            return $this->getWeeklyData($query);
        }

        return $this->getDailyData($query);
    }

    protected function getGroupingStrategy(?int $days): string
    {
        if ($days === null || $days > 365) {
            return 'month';
        } elseif ($days > 90) {
            return 'week';
        }

        return 'day';
    }

    protected function getDailyData($query): array
    {
        $votes = $query
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();

        if ($votes->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $startDate = Carbon::parse($votes->first()->date);
        $endDate = Carbon::parse($votes->last()->date);

        $labels = [];
        $data = [];
        $votesMap = $votes->pluck('count', 'date')->toArray();

        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('M d');
            $data[] = $votesMap[$dateKey] ?? 0;
            $currentDate->addDay();
        }

        return $this->formatChartData($labels, $data);
    }

    protected function getWeeklyData($query): array
    {
        $votes = $query->orderBy('created_at')->get();

        if ($votes->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $grouped = $votes->groupBy(fn ($vote) => Carbon::parse($vote->created_at)->startOfWeek()->format('Y-m-d'));

        $labels = [];
        $data = [];

        foreach ($grouped as $weekStart => $weekVotes) {
            $labels[] = Carbon::parse($weekStart)->format('M d');
            $data[] = $weekVotes->count();
        }

        return $this->formatChartData($labels, $data);
    }

    protected function getMonthlyData($query): array
    {
        $votes = $query->orderBy('created_at')->get();

        if ($votes->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $grouped = $votes->groupBy(fn ($vote) => Carbon::parse($vote->created_at)->format('Y-m'));

        $labels = [];
        $data = [];

        foreach ($grouped as $month => $monthVotes) {
            $labels[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
            $data[] = $monthVotes->count();
        }

        return $this->formatChartData($labels, $data);
    }

    protected function formatChartData(array $labels, array $data): array
    {
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
