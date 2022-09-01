<?php

namespace App\Http\Livewire;

use Closure;
use Filament\Tables;
use Livewire\Component;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;

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
            Tables\Columns\TextColumn::make('content')->wrap()->label(trans('table.content'))->searchable(),
            Tables\Columns\TextColumn::make('item.title')->label(trans('table.title'))->searchable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime(Auth()->user()->date_time_format)->sortable()->label(trans('table.created_at')),
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
