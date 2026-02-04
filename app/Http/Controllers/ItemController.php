<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Project;
use App\Enums\ItemActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Settings\GeneralSettings;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Http\RedirectResponse;
use Spatie\Activitylog\Models\Activity;
use Filament\Notifications\Notification;

class ItemController extends Controller
{
    public function show($projectId, $itemId = null)
    {
        $project = null;

        if (!$itemId) {
            $item = Item::query()->visibleForCurrentUser()->where('slug', $projectId)->firstOrFail();

            if ($item->project) {
                // Looks like this item is added to the project, let's redirect to the correct view for the item.
                return redirect()->to($item->view_url);
            }
        } else {
            $project = Project::query()->visibleForCurrentUser()->where('slug', $projectId)->firstOrFail();

            $item = $project->items()->visibleForCurrentUser()->where('slug', $itemId)->firstOrFail();
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

        return view('item', [
            'project' => $project,
            'board' => $item->board,
            'item' => $item,
            'user' => $item->user,
            'activities' => $activities,
        ]);
    }

    public function ai(Request $request, $projectSlug, $itemSlug): JsonResponse|Response
    {
        $project = Project::query()->visibleForCurrentUser()->where('slug', $projectSlug)->firstOrFail();
        $item = $project->items()->visibleForCurrentUser()->where('slug', $itemSlug)->firstOrFail();

        $data = [
            'title' => $item->title,
            'content' => $item->content,
            'board' => $item->board?->title,
            'project' => $project->title,
            'votes' => $item->total_votes,
            'tags' => $item->tags->pluck('name')->toArray(),
        ];

        $includes = $request->query('include', []);

        if (!empty($includes['comments'])) {
            $data['comments'] = $item->comments()
                ->with('user:id,name,username')
                ->whereNull('parent_id')
                ->where('private', false)
                ->oldest()
                ->get()
                ->map(fn ($comment) => [
                    'author' => $comment->user->name ?? $comment->user->username,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->toIso8601String(),
                ])
                ->toArray();
        }

        return match ($request->query('format', 'json')) {
            'yml', 'yaml' => response(Yaml::dump($data, 4, 2), 200, ['Content-Type' => 'text/yaml']),
            'markdown', 'md' => response($this->toMarkdown($data), 200, ['Content-Type' => 'text/markdown']),
            default => response()->json($data),
        };
    }

    protected function toMarkdown(array $data): string
    {
        $md = "# {$data['title']}\n\n";
        $md .= "**Project:** {$data['project']}";
        $md .= $data['board'] ? " | **Board:** {$data['board']}" : '';
        $md .= " | **Votes:** {$data['votes']}\n";

        if (!empty($data['tags'])) {
            $md .= '**Tags:** ' . implode(', ', $data['tags']) . "\n";
        }

        $md .= "\n---\n\n{$data['content']}\n";

        if (!empty($data['comments'])) {
            $md .= "\n---\n\n## Comments\n\n";
            foreach ($data['comments'] as $comment) {
                $md .= "**{$comment['author']}** ({$comment['created_at']}):\n{$comment['content']}\n\n";
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
