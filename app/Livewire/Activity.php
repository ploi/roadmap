<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Tables\Table;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;

class Activity extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithTable, InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActivityModel::query()
                    ->with(['causer', 'subject.user', 'subject.comments'])
                    ->where(function (Builder $query) {
                        $query->whereHasMorph('subject', ['App\Models\Item'], function (Builder $query) {
                            $query->where('private', false);
                        })
                        ->orWhereHasMorph('subject', ['App\Models\Comment'], function (Builder $query) {
                            $query->where('private', false);
                        });
                    })
                    ->whereNotNull('causer_id')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('causer.name')
                    ->label(trans('table.users'))
                    ->url(function ($record) {
                        if ($causer = $record->causer) {
                            return route('public-user', $causer->username);
                        }

                        return null;
                    }),
                TextColumn::make('description')
                    ->label(trans('table.activity'))
                    ->formatStateUsing(function ($state, $record) {
                        $subject = $record->subject;

                        if (!$subject) {
                            return ucfirst($state);
                        }

                        if ($subject instanceof \App\Models\Item) {
                            $title = str($subject->title)->limit(50);
                            return ucfirst("{$state}: \"{$title}\"");
                        }

                        if ($subject instanceof \App\Models\Comment && $subject->item) {
                            $title = str($subject->item->title)->limit(50);
                            return ucfirst("commented on \"{$title}\"");
                        }

                        return ucfirst($state);
                    }),
                TextColumn::make('subject.total_votes')
                    ->label(trans('table.votes'))
                    ->default(0)
                    ->formatStateUsing(function ($state, $record) {
                        $subject = $record->subject;

                        if (!$subject) {
                            return 0;
                        }

                        return $subject->total_votes ?? 0;
                    }),
                TextColumn::make('comments_count')
                    ->label(trans('table.comments'))
                    ->formatStateUsing(function ($state, $record) {
                        $subject = $record->subject;

                        if (!$subject || !$subject instanceof \App\Models\Item) {
                            return 0;
                        }

                        return $subject->comments->count();
                    })
                    ->default(0),
                TextColumn::make('created_at')
                    ->label(trans('table.date'))
                    ->dateTime()
                    ->since(),
            ])
            ->recordUrl(function ($record) {
                $subject = $record->subject;

                if (!$subject) {
                    return null;
                }

                if ($subject instanceof \App\Models\Item) {
                    if (!$subject->board || !$subject->project) {
                        return route('items.show', $subject);
                    }

                    return route('projects.items.show', [$subject->project, $subject]);
                }

                if ($subject instanceof \App\Models\Comment && $subject->item) {
                    return route('items.show', $subject->item) . "#comment-{$subject->id}";
                }

                return null;
            })
            ->defaultSort('created_at', 'desc');
    }

    public function render()
    {
        return view('livewire.activity');
    }
}
