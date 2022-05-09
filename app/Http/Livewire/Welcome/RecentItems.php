<?php

namespace App\Http\Livewire\Welcome;

use App\Models\Item;
use Closure;
use Filament\Tables;
use Livewire\Component;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;

class RecentItems extends Component implements HasTable
{
    use InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Item::query()->with('board.project')->limit(10)->latest();
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function ($record) {
            if (!$record->board) {
                return route('items.show', $record->id);
            }

            if (!$record->project) {
                return route('items.show', $record->id);
            }

            return route('projects.items.show', [$record->project->id, $record->id]);
        };
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('total_votes')->label('Votes')->sortable(),
            Tables\Columns\TextColumn::make('board.project.title')->label('Project'),
            Tables\Columns\TextColumn::make('board.title'),
        ];
    }

    public function render()
    {
        return view('livewire.welcome.recent-items');
    }
}
