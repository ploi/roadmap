<?php

namespace App\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Tables\Columns\TextColumn;
use Closure;
use Filament\Tables\Table;
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

    public function table(Table $table): Table
    {
        return $table
            ->query(auth()->user()->mentions()->latest('mentions.created_at')->getQuery())
            ->columns([
                TextColumn::make('content')->wrap()->label(trans('table.content'))->searchable(),
                TextColumn::make('item.title')->label(trans('table.title'))->searchable(),
                TextColumn::make('created_at')->dateTime()->sortable()->label(trans('table.created_at')),
            ])
            ->recordUrl(function ($record) {
                return route('items.show', [$record->item]) . '#comment-' . $record->model_id;
            })
            ->paginated(auth()->user()->per_page_setting ?? [5]);
    }

    public function render()
    {
        return view('livewire.recent-mentions');
    }
}
