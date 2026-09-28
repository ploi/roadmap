<?php

namespace App\Livewire\Changelog;

use App\Models\Tag;
use Livewire\Component;
use App\Models\Changelog;
use App\Models\Item as ItemModel;
use App\Settings\GeneralSettings;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Item extends Component
{
    public Changelog $changelog;

    public function render(): View
    {
        $includedItems = app(GeneralSettings::class)->show_changelog_related_items
            ? $this->includedItems()
            : collect();

        return view('livewire.changelog.item', [
            'includedItemsCount' => $includedItems->count(),
            'includedItemGroups' => $this->groupByChangelogTag($includedItems),
        ]);
    }

    /**
     * The items included in the changelog that the current user can see.
     *
     * @return Collection<int, ItemModel>
     */
    protected function includedItems(): Collection
    {
        return $this->changelog->items()
            ->visibleForCurrentUser()
            ->with(['board', 'project', 'tags' => fn (MorphToMany $query) => $query->where('changelog', true)])
            ->orderByDesc('total_votes')
            ->orderBy('title')
            ->get();
    }

    /**
     * Group the items by their changelog tags, items without a changelog tag come first in a group without a title.
     *
     * @param  Collection<int, ItemModel>  $items
     * @return Collection<int, array{title: ?string, items: Collection<int, ItemModel>}>
     */
    protected function groupByChangelogTag(Collection $items): Collection
    {
        $itemsWithoutTag = $items->filter(fn (ItemModel $item) => $item->tags->isEmpty());

        $tagGroups = $items
            ->flatMap(fn (ItemModel $item) => $item->tags->map(fn (Tag $tag) => ['tag' => $tag, 'item' => $item]))
            ->groupBy(fn (array $entry) => $entry['tag']->id)
            ->map(fn (Collection $entries) => [
                'title' => $entries->first()['tag']->name,
                'items' => $entries->pluck('item'),
            ])
            ->sortBy('title')
            ->values();

        return collect([['title' => null, 'items' => $itemsWithoutTag->values()]])
            ->filter(fn (array $group) => $group['items']->isNotEmpty())
            ->concat($tagGroups)
            ->values();
    }
}
