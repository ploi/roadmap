<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Collection;

class PublicUserController extends Controller
{
    public function __invoke($userName)
    {
        $user = User::where('username', $userName)->firstOrFail();

        $activities = $this->getRecentActivities($user);

        $data = [
            'items_created' => $user->items->count(),
            'comments_created' => $user->comments->count(),
            'votes_created' => $user->votes->count(),
            'activities' => $activities,
        ];

        return view('public-user', ['user' => $user, 'data' => $data]);
    }

    private function getRecentActivities(User $user, int $limit = 20): Collection
    {
        $items = $user->items()
            ->with('project')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'item',
                    'title' => $item->title,
                    'content' => $item->content,
                    'url' => route('items.show', $item),
                    'project' => $item->project->title ?? null,
                    'created_at' => $item->created_at,
                ];
            });

        $comments = $user->comments()
            ->with('item.project')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($comment) {
                return [
                    'type' => 'comment',
                    'title' => $comment->item->title ?? null,
                    'content' => $comment->content,
                    'url' => route('items.show', $comment->item),
                    'project' => $comment->item->project->title ?? null,
                    'created_at' => $comment->created_at,
                ];
            });

        $votes = $user->votes()
            ->with(['model' => function ($query) {
                $query->when(
                    fn ($q) => $q->getModel() instanceof Item,
                    fn ($q) => $q->with('project')
                )->when(
                    fn ($q) => $q->getModel() instanceof Comment,
                    fn ($q) => $q->with('item.project')
                );
            }])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($vote) {
                if ($vote->model instanceof Item) {
                    return [
                        'type' => 'vote',
                        'title' => $vote->model->title,
                        'url' => route('items.show', $vote->model),
                        'project' => $vote->model->project->title ?? null,
                        'created_at' => $vote->created_at,
                    ];
                }
                if ($vote->model instanceof Comment) {
                    return [
                        'type' => 'vote',
                        'title' => $vote->model->item->title ?? null,
                        'content' => $vote->model->content,
                        'url' => route('items.show', $vote->model->item),
                        'project' => $vote->model->item->project->title ?? null,
                        'created_at' => $vote->created_at,
                    ];
                }
                return null;
            })
            ->filter();

        return collect()
            ->concat($items)
            ->concat($comments)
            ->concat($votes)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values();
    }
}
