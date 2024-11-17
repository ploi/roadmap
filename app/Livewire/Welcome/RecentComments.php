<?php

namespace App\Livewire\Welcome;

use Closure;
use Filament\Tables;
use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class RecentComments extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    /**
     * @return Builder<Comment>
     */
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

    /**
     * Get the table columns.
     *
     * @return Tables\Columns\TextColumn[]
     */
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('content')->label(trans('table.content')),
            Tables\Columns\TextColumn::make('item.title')->label(trans('table.item')),
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

    public function render(): View
    {
        return view('livewire.welcome.recent-comments');
    }
}
