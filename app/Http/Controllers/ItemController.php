<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Project;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function show($projectId, $itemId = null)
    {
        $project = null;

        if (!$itemId) {
            $item = Item::query()->where('slug', $projectId)->firstOrFail();
        } else {
            $project = Project::query()->where('slug', $projectId)->firstOrFail();

            $item = $project->items()->where('items.slug', $itemId)->firstOrFail();
        }

        return view('item', [
            'project' => $project,
            'board' => $item->board,
            'item' => $item,
            'user' => $item->user,
            'activities' => $item->activities()->with('causer')->latest()->limit(10)->get()
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
