<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Project;
use App\Enums\ItemActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Settings\GeneralSettings;
use Illuminate\Http\RedirectResponse;
use Spatie\Activitylog\Models\Activity;
use Filament\Notifications\Notification;

class ItemController extends Controller
{
    public function show(Request $request, $projectId, $itemId = null)
    {
        if ($request->prefers(['text/html', 'text/markdown']) === 'text/markdown') {
            return $this->markdown($projectId, $itemId);
        }

        $item = $this->findItem($projectId, $itemId);

        if (!$itemId && $item->project) {
            // Looks like this item is added to the project, let's redirect to the correct view for the item.
            return redirect()->to($item->view_url);
        }

        $showGitHubLink = app(GeneralSettings::class)->show_github_link;
        $activities = $item->activities()->with('causer')->latest()->limit(10)->get()->filter(function (Activity $activity) use ($showGitHubLink) {
            if (!$showGitHubLink && ItemActivity::getForActivity($activity) === ItemActivity::LinkedToIssue) {
                return false;
            }

            return true;
        })->map(function (Activity $activity) {
            $itemActivity = ItemActivity::getForActivity($activity);

            if ($itemActivity !== null) {
                $activity->description = $itemActivity->getTranslation($activity->properties->get('attributes'));
            }

            return $activity;
        });

        return response()->view('item', [
            'project' => $item->project,
            'board' => $item->board,
            'item' => $item,
            'user' => $item->user,
            'activities' => $activities,
        ])->header('Vary', 'Accept');
    }

    public function markdown($projectId, $itemId = null): Response|RedirectResponse
    {
        $item = $this->findItem($projectId, $itemId);

        if (!$itemId && $item->project) {
            return redirect()->to($item->markdown_url);
        }

        return response($this->toMarkdown($item), 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Vary' => 'Accept',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    /**
     * The old AI endpoint, superseded by the ".md" variant of the item URL.
     */
    public function ai($projectSlug, $itemSlug): RedirectResponse
    {
        return redirect()->route('projects.items.markdown', [
            'project' => $projectSlug,
            'item' => $itemSlug,
        ], 301);
    }

    protected function findItem(string $projectSlug, ?string $itemSlug): Item
    {
        if (!$itemSlug) {
            return Item::query()->visibleForCurrentUser()->where('slug', $projectSlug)->firstOrFail();
        }

        $project = Project::query()->visibleForCurrentUser()->where('slug', $projectSlug)->firstOrFail();

        return $project->items()->visibleForCurrentUser()->where('slug', $itemSlug)->firstOrFail()->setRelation('project', $project);
    }

    protected function toMarkdown(Item $item): string
    {
        $meta = array_filter([
            $item->project ? "**Project:** {$item->project->title}" : null,
            $item->board ? "**Board:** {$item->board->title}" : null,
            "**Votes:** {$item->total_votes}",
        ]);

        $md = "# {$item->title}\n\n";
        $md .= implode(' | ', $meta) . "\n";

        if ($item->tags->isNotEmpty()) {
            $md .= '**Tags:** ' . $item->tags->pluck('name')->implode(', ') . "\n";
        }

        $md .= "**URL:** {$item->view_url}\n";
        $md .= "\n---\n\n{$item->content}\n";

        $comments = $item->comments()
            ->with('user:id,name,username')
            ->whereNull('parent_id')
            ->where('private', false)
            ->oldest()
            ->get();

        if ($comments->isNotEmpty()) {
            $md .= "\n---\n\n## Comments\n\n";

            foreach ($comments as $comment) {
                $author = $comment->user?->name ?? $comment->user?->username;
                $md .= "**{$author}** ({$comment->created_at->toIso8601String()}):\n{$comment->content}\n\n";
            }
        }

        return $md;
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

        abort_if($item->board?->block_votes, 403);

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
