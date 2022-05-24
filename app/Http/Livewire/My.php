<?php

namespace App\Http\Livewire;

use Closure;
use Filament\Tables;
use Livewire\Component;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;

class My extends Component implements HasTable
{
    use InteractsWithTable;

    public $type = 'default';

    protected function getTableQuery(): Builder
    {
        if ($this->type == 'default') {
            return auth()->user()->items()->with('board.project')->latest()->getQuery();
        }

        return auth()->user()->votedItems()->with('board.project')->latest('votes.created_at')->getQuery();
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\TextColumn::make('total_votes')->label('Votes')->sortable(),
            Tables\Columns\TextColumn::make('board.project.title')->label('Project'),
            Tables\Columns\TextColumn::make('board.title'),
            Tables\Columns\TextColumn::make('created_at')->sortable()->label('Date')->dateTime(),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function ($record) {
            if (!$record->board) {
                return route('items.show', $record);
            }

            if (!$record->project) {
                return route('items.show', $record);
            }

            return route('projects.items.show', [$record->project, $record]);
        };
    }

    public function render()
    {
        return view('livewire.my');
    }
}
