<?php

namespace App\Filament\Resources\InboxResource\Pages;

use App\Models\Board;
use App\Models\Project;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\InboxResource;

class EditInbox extends EditRecord
{
    protected static string $resource = InboxResource::class;

    protected function beforeSave(): void
    {
        if ($this->data['board_id'] != $this->record->board_id) {
            $board = Board::find($this->data['board_id']);

            activity()
                ->performedOn($this->record)
                ->log('moved item to board ' . $board->title);
        }

        if ($this->data['project_id'] != $this->record->project_id) {
            $project = Project::find($this->data['project_id']);

            activity()
                ->performedOn($this->record)
                ->log('moved item to project ' . $project->title);
        }
    }
}
