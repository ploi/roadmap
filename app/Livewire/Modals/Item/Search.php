<?php

namespace App\Livewire\Modals\Item;

use App\Models\Item;
use Livewire\Component;
use Filament\Tables\Table;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class Search extends Component implements HasTable, HasForms
{
    use InteractsWithForms, InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->query(Item::query()->limit(10))
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn(Item $record): string => route('items.show', $record->slug))
            ->columns([
                TextColumn::make('title')->wrap()->searchable(),
                TextColumn::make('votes_count')->counts('votes')->label(trans('table.total-votes')),
                TextColumn::make('created_at')->dateTime('Y-m-d H:i:s')->label('Date'),
            ]);
    }

    public function render()
    {
        return view('livewire.modals.item.search');
    }
}
