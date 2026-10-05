<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Project;

class BoardsController extends Controller
{
    public function show(Project $project, Board $board)
    {
        abort_unless($project->isVisibleForCurrentUser(), 404);
        abort_unless($board->visible, 404);

        return view('board', [
            'project' => $project,
            'board' => $board,
        ]);
    }
}
