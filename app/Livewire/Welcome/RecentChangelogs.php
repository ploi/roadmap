<?php

namespace App\Livewire\Welcome;

use Livewire\Component;
use App\Models\Changelog;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions\Contracts\HasActions;
use App\Http\Controllers\ChangelogController;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;

class RecentChangelogs extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithTable, InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Changelog::query()->published()->with('project')->limit(10))
            ->columns([
                TextColumn::make('title')->label(trans('table.title')),
                TextColumn::make('project.title')->label(trans('table.project'))
                    ->state(fn (Changelog $record) => $record->project?->isVisibleForCurrentUser() ? $record->project->title : null)
                    ->url(fn (Changelog $record) => $record->project?->isVisibleForCurrentUser() ? ChangelogController::overviewUrl($record->project->slug) : null),
                TextColumn::make('published_at')->label(trans('table.published_at'))->date(),
            ])
            ->recordUrl(fn (Changelog $record) => route('changelog.show', $record))
            ->paginated(false)
            ->defaultKeySort(false);
    }

    public function render(): View
    {
        return view('livewire.welcome.recent-changelogs');
    }
}
