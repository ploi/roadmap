<?php

namespace App\Livewire\Welcome;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Tables\Columns\TextColumn;
use Closure;
use Filament\Tables;
use App\Models\Comment;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class RecentComments extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithTable, InteractsWithForms;

    protected function getTableQuery(): Builder
    {
        return Comment::query()->public()->limit(10);
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function ($record) {
            if ($item = $record->item) {
                return route('items.show', $item). "#comment-$record->id";
            }

            return null;
        };
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('content')->label(trans('table.content')),
            TextColumn::make('item.title')->label(trans('table.item')),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function render()
    {
        return view('livewire.welcome.recent-comments');
    }
}
