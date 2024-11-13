<?php

namespace App\Livewire;

use App\Models\Comment;
use Closure;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Xetaio\Mentions\Models\Mention;

class RecentMentions extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    /**
     * @return Builder<Comment>|null
     */
    protected function getTableQuery(): Builder|null
    {
        return auth()->user()?->mentions()->latest('mentions.created_at')->getQuery();
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return auth()->user()->per_page_setting ?? [5];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('content')->wrap()->label(trans('table.content'))->searchable(),
            Tables\Columns\TextColumn::make('item.title')->label(trans('table.title'))->searchable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->label(trans('table.created_at')),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function ($record) {
            return route('items.show', [$record->item]) . '#comment-' . $record->model_id;
        };
    }

    public function render(): View
    {
        return view('livewire.recent-mentions');
    }
}
