<?php

namespace App\Http\Controllers;

use App\Enums\ItemActivity;
use App\Models\Item;
use App\Models\Project;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ItemController extends Controller
{
    public function show($projectId, $itemId = null)
    {
        $project = null;

        if (!$itemId) {
            $item = Item::query()->visibleForCurrentUser()->where('slug', $projectId)->firstOrFail();
        } else {
            $project = Project::query()->where('slug', $projectId)->firstOrFail();

            $item = $project->items()->visibleForCurrentUser()->where('items.slug', $itemId)->firstOrFail();
        }

        $activities = $item->activities()->with('causer')->latest()->limit(10)->get()->map(function (Activity $activity) {
            $itemActivity = ItemActivity::getForActivity($activity);

            if ($itemActivity !== null) {
                $activity->description = $itemActivity->getTranslation($activity->properties->get('attributes'));
            }

            return $activity;
        });

        return view('item', [
            'project' => $project,
            'board' => $item->board,
            'item' => $item,
            'user' => $item->user,
            'activities' => $activities,
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
