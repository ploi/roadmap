<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Models\Board;
use App\Models\Project;
use App\Filament\Resources\ItemResource;
use App\Models\User;
use App\Notifications\Item\ItemUpdatedNotification;
use Filament\Resources\Pages\EditRecord;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    protected function beforeSave(): void
    {
        $hasAnUpdate = false;

        if ($this->data['board_id'] != $this->record->board_id) {
            $board = Board::find($this->data['board_id']);

            activity()
                ->performedOn($this->record)
                ->log('moved item to board ' . $board->title);

            $hasAnUpdate = true;
        }

        if ($this->data['project_id'] != $this->record->project_id) {
            $project = Project::find($this->data['project_id']);

            activity()
                ->performedOn($this->record)
                ->log('moved item to project ' . $project->title);

            $hasAnUpdate = true;
        }

        if ($hasAnUpdate) {
            $users = $this->record->subscribedVotes()->with('user')->get()->pluck('user');

            $users->each(function(User $user){
               $user->notify(new ItemUpdatedNotification($this->record));
            });
        }
    }
}
