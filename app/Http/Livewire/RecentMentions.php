<?php

namespace App\Http\Livewire;

use Closure;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Filament\Tables;

class RecentMentions extends Component implements HasTable
{
    use InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return auth()->user()->mentions()->latest('mentions.created_at')->getQuery();
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('content')->searchable(),
            Tables\Columns\TextColumn::make('item.title')->searchable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->label('Date'),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function ($record) {
            return route('items.show', [$record->item]) . '#comment-' . $record->model_id;
        };
    }

    public function render()
    {
        return view('livewire.recent-mentions');
    }
}
