<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Project;
use App\Enums\ItemActivity;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
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
            $project = Project::query()->visibleForCurrentUser()->where('slug', $projectId)->firstOrFail();

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
            'item' => $item->load('tags'),
            'user' => $item->user,
            'activities' => $activities,
        ]);
    }

    public function edit($id)
    {
        $item = auth()->user()->items()->findOrFail($id);

        return view('edit-item', [
            'item' => $item
        ]);
    }

    public function vote(Request $request, $projectId, $itemId)
    {
        $project = Project::findOrFail($projectId);

        $item = $project->items()->visibleForCurrentUser()->findOrfail($itemId);

        $item->toggleUpvote();

        return redirect()->back();
    }

    public function updateBoard(Project $project, Item $item, Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasAdminAccess(), 403);

        $item->update($request->only('board_id'));

        Notification::make()
                    ->title(trans('items.update-board-success', ['board' => $item->board->title]))
                    ->success()
                    ->send();

        return redirect()->back();
    }
}
