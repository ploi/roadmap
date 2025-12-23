<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;

class My extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithTable, InteractsWithForms;

    public $type = 'default';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('title')->wrap()->label(trans('table.title'))->searchable(),
                TextColumn::make('total_votes')->label(trans('table.total-votes'))->sortable(),
                TextColumn::make('project.title')->label(trans('table.project')),
                TextColumn::make('board.title')->label(trans('table.board')),
                TextColumn::make($this->type === 'commentedOn' ? 'comments_max_created_at' : 'created_at')
                    ->sortable()
                    ->label(function () {
                        if ($this->type === 'commentedOn') {
                            return trans('table.last_comment_posted_at');
                        }

                        return trans('table.created_at');
                    })
                    ->formatStateUsing(fn (Carbon|string $state) => (is_string($state) ? Carbon::parse($state) : $state)->isoFormat('L LTS')),
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
            ->paginated(auth()->user()->per_page_setting ?? [5]);
    }

    protected function getTableQuery(): Builder
    {
        if ($this->type == 'default') {
            return auth()->user()->items()->with('board.project')->getQuery();
        }

        if ($this->type == 'commentedOn') {
            return auth()->user()->commentedItems()->getQuery();
        }

        return auth()->user()->votedItems()->with('board.project')->latest('votes.created_at')->getQuery();
    }

    public function render()
    {
        return view('livewire.my');
    }
}
