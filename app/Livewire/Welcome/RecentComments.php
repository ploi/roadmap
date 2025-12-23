<?php

namespace App\Livewire\Welcome;

use App\Models\Comment;
use Livewire\Component;
use Filament\Tables\Table;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;

class RecentComments extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithTable, InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Comment::query()->public()->limit(10))
            ->columns([
                TextColumn::make('content')->label(trans('table.content')),
                TextColumn::make('item.title')->label(trans('table.item')),
            ])
            ->recordUrl(function ($record) {
                if ($item = $record->item) {
                    return route('items.show', $item). "#comment-$record->id";
                }

                return null;
            })
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }

    public function render()
    {
        return view('livewire.welcome.recent-comments');
    }
}
