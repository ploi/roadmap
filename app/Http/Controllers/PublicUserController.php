<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Collection;

class PublicUserController extends Controller
{
    public const ACTIVITY_ITEM_COUNT = 20;

    public function __invoke($userName)
    {
        $user = User::where('username', $userName)->firstOrFail();

        $activities = $this->getRecentActivities($user);

        $data = [
            'items_created' => $user->items()->visibleForCurrentUser()->count(),
            'comments_created' => $user->comments()
                ->whereHas('item', fn ($q) => $q->visibleForCurrentUser())
                ->count(),
            'votes_created' => $user->votes()
                ->whereHasMorph('model', [Item::class, Comment::class], function ($query, $type) {
                    if ($type === Item::class) {
                        $query->visibleForCurrentUser();
                    } elseif ($type === Comment::class) {
                        $query->whereHas('item', fn ($q) => $q->visibleForCurrentUser());
                    }
                })
                ->count(),
            'activities' => $activities,
        ];

        return view('public-user', ['user' => $user, 'data' => $data]);
    }

    private function getRecentActivities(User $user): Collection
    {
        $items = $user->items()
            ->visibleForCurrentUser()
            ->with('project')
            ->latest()
            ->limit(self::ACTIVITY_ITEM_COUNT)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'item',
                    'title' => $item->title,
                    'content' => $item->content,
                    'url' => route('items.show', $item),
                    'project' => $item->project?->title,
                    'created_at' => $item->created_at,
                ];
            });

        $comments = $user->comments()
            ->whereHas('item', fn ($query) => $query->visibleForCurrentUser())
            ->with(['item' => fn ($q) => $q->with('project')])
            ->latest()
            ->limit(self::ACTIVITY_ITEM_COUNT)
            ->get()
            ->map(function ($comment) {
                return [
                    'type' => 'comment',
                    'title' => $comment->item?->title,
                    'content' => $comment->content,
                    'url' => $comment->item ? route('items.show', $comment->item) : null,
                    'project' => $comment->item?->project?->title,
                    'created_at' => $comment->created_at,
                ];
            })
            ->filter(fn ($comment) => $comment['url'] !== null);

        $votes = $user->votes()
            ->whereHasMorph('model', [Item::class, Comment::class], function ($query, $type) {
                if ($type === Item::class) {
                    $query->visibleForCurrentUser();
                } elseif ($type === Comment::class) {
                    $query->whereHas('item', fn ($q) => $q->visibleForCurrentUser());
                }
            })
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
            ->limit(self::ACTIVITY_ITEM_COUNT)
            ->get()
            ->map(function ($vote) {
                if ($vote->model instanceof Item) {
                    return [
                        'type' => 'vote',
                        'title' => $vote->model->title,
                        'url' => route('items.show', $vote->model),
                        'project' => $vote->model->project?->title,
                        'created_at' => $vote->created_at,
                    ];
                }
                if ($vote->model instanceof Comment) {
                    return [
                        'type' => 'vote',
                        'title' => $vote->model->item?->title,
                        'content' => $vote->model->content,
                        'url' => $vote->model->item ? route('items.show', $vote->model->item) : null,
                        'project' => $vote->model->item?->project?->title,
                        'created_at' => $vote->created_at,
                    ];
                }
                return null;
            })
            ->filter(fn ($vote) => $vote !== null && ($vote['url'] ?? null) !== null);

        return collect()
            ->concat($items)
            ->concat($comments)
            ->concat($votes)
            ->sortByDesc('created_at')
            ->take(self::ACTIVITY_ITEM_COUNT)
            ->values();
    }
}
