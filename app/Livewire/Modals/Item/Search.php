<?php

namespace App\Livewire\Modals\Item;

use App\Models\Item;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class Search extends Component implements HasTable, HasForms
{
    use InteractsWithForms, InteractsWithTable;


    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->query(Item::query())
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('votes_count')->counts('votes')->label(trans('table.total-votes')),
                TextColumn::make('created_at')->dateTime('Y-m-d H:i:s')->label('Date'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('livewire.modals.item.search');
    }
}
