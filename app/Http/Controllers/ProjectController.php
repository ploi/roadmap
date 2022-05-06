<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function show($id)
    {
        $project = Project::findOrFail($id);

        return view('project', [
            'project' => $project,
            'boards' => $project->boards()->with(['items' => function($query){
                return $query->popular()->withCount('votes');
            }])->get()
        ]);
    }
}
