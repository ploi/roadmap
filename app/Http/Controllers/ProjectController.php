<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function show($id)
    {
        $project = Project::query()->visibleForCurrentUser()->where('slug', $id)->firstOrFail();

        return view('project', [
            'project' => $project,
            'boards' => $project->boards()->visible()->get(),
        ]);
    }
}
