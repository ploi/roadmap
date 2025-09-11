<?php

namespace App\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Tables\Columns\TextColumn;
use Closure;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class RecentMentions extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithTable, InteractsWithForms;

    protected function getTableQuery(): Builder
    {
        return auth()->user()->mentions()->latest('mentions.created_at')->getQuery();
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return auth()->user()->per_page_setting ?? [5];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('content')->wrap()->label(trans('table.content'))->searchable(),
            TextColumn::make('item.title')->label(trans('table.title'))->searchable(),
            TextColumn::make('created_at')->dateTime()->sortable()->label(trans('table.created_at')),
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
