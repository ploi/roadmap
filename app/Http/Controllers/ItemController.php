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
            'item' => $item
        ]);
    }

    public function vote(Request $request, $projectId, $itemId)
    {
        $project = Project::findOrFail($projectId);

        $item = $project->items()->findOrfail($itemId);

        $check = $item->votes()->where('user_id', $request->user()->id)->exists();

        if ($check) {
            return redirect()->back();
        }

        $vote = $item->votes()->create();
        $vote->user()->associate($request->user())->save();

        return redirect()->back();
    }
}
