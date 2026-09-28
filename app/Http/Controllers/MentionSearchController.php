<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class MentionSearchController extends Controller
{
    public function __invoke(Request $request): array
    {
        $search = trim((string) $request->input('query'));
        $participantIds = $this->participantIds($request->integer('item'));

        if ($search === '' && $participantIds->isEmpty()) {
            return [];
        }

        return User::query()
            ->whereKeyNot(auth()->id())
            ->when(
                $search === '',
                fn (Builder $query) => $query->whereKey($participantIds),
                fn (Builder $query) => $query->where(fn (Builder $query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%"))
            )
            ->when($participantIds->isNotEmpty(), fn (Builder $query) => $query->orderByRaw(
                'CASE WHEN id IN (' . $participantIds->map(fn () => '?')->implode(',') . ') THEN 0 ELSE 1 END',
                $participantIds->all()
            ))
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'username', 'email'])
            ->map(function (User $user) {
                return [
                    'key' => $user->name,
                    'value' => $user->username,
                    'avatar' => $user->getGravatar()
                ];
            })
            ->toArray();
    }

    /**
     * The author and commenters of the item being commented on, which are suggested first.
     *
     * @return Collection<int, int>
     */
    private function participantIds(int $itemId): Collection
    {
        $item = $itemId ? Item::query()->visibleForCurrentUser()->find($itemId) : null;

        if (! $item) {
            return collect();
        }

        return $item->comments()
            ->when(! auth()->user()->hasAdminAccess(), fn ($query) => $query->where('private', false))
            ->pluck('user_id')
            ->push($item->user_id)
            ->filter()
            ->unique()
            ->values();
    }
}
