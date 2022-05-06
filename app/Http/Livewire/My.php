<?php

namespace App\Http\Livewire;

use Closure;
use App\Models\Item;
use Filament\Tables;
use Livewire\Component;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;

class My extends Component implements HasTable
{
    use InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Item::query()->where('user_id', auth()->id())->orderBy('created_at', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\TextColumn::make('total_votes')->label('Votes')->sortable(),
            Tables\Columns\TextColumn::make('board.title'),
            Tables\Columns\TextColumn::make('created_at')->sortable()->label('Date')->dateTime(),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn ($record) => /*route('projects.items.show', $record)*/ null;
    }

    public function render()
    {
        return view('livewire.my');
    }
}
