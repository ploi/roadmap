<?php

namespace App\Livewire\Welcome;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Closure;
use App\Models\Item;
use Livewire\Component;
use Illuminate\Support\Arr;
use App\Settings\GeneralSettings;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class RecentItems extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithTable, InteractsWithForms;

    public function table(Table $table): Table
    {
        $recentItemsConfig = collect(app(GeneralSettings::class)->dashboard_items)->first();

        return $table
            ->query(
                Item::query()
                    ->with('board.project')
                    ->visibleForCurrentUser()
                    ->when(Arr::get($recentItemsConfig, 'must_have_board'), function (Builder $query) {
                        return $query->has('board');
                    })
                    ->when(Arr::get($recentItemsConfig, 'must_have_project'), function (Builder $query) {
                        return $query->has('project');
                    })
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('title')->label(trans('table.title')),
                TextColumn::make('total_votes')->label(trans('table.total-votes'))->sortable(),
                TextColumn::make('project.title')->label(trans('table.project'))
                    ->url(function ($record) {
                        if ($project = $record->project) {
                            return route('projects.show', $project);
                        }

                        return null;
                    }),
                TextColumn::make('board.title')->label(trans('table.board'))
                    ->url(function ($record) {
                        if ($board = $record->board) {
                            return route('projects.boards.show', [$record->project, $board]);
                        }

                        return null;
                    }),
            ])
            ->recordUrl(function ($record) {
                if (!$record->board) {
                    return route('items.show', $record);
                }

                if (!$record->project) {
                    return route('items.show', $record);
                }

                return route('projects.items.show', [$record->project, $record]);
            })
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }

    public function render()
    {
        return view('livewire.welcome.recent-items');
    }
}
