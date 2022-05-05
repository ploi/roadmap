<?php

namespace App\Http\Livewire;

use App\Models\Item;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Filament\Tables;

class My extends Component implements HasTable
{
    use InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Item::query()->where('user_id', auth()->id());
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('total_votes')->label('Votes')->sortable(),
            Tables\Columns\TextColumn::make('created_at')->sortable()->label('Date')->dateTime(),
        ];
    }

    public function render()
    {
        return view('livewire.my');
    }
}
