<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function show($projectId, $itemId)
    {
        $project = Project::findOrFail($projectId);

        $item = $project->items()->findOrfail($itemId);

        return view('item', [
            'project' => $project,
            'board' => $item->board,
            'item' => $item,
            'user' => $item->user
        ]);
    }

    public function vote(Request $request, $projectId, $itemId)
    {
        $project = Project::findOrFail($projectId);

        $item = $project->items()->findOrfail($itemId);

        $item->toggleUpvote();

        return redirect()->back();
    }
}
