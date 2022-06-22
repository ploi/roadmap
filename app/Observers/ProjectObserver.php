<?php

namespace App\Observers;

use App\Models\Board;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class ProjectObserver
{
    public function deleting(Project $project)
    {
        try {
            Storage::delete('public/og-' . $project->slug . '-' . $project->id . '.jpg');
        } catch (\Throwable $exception) {

        }

        $project->boards->each(fn(Board $board) => $board->delete());
    }
}
