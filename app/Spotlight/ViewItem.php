<?php

namespace App\Spotlight;

use App\Models\Item;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightSearchResult;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightCommandDependencies;

class ViewItem extends SpotlightCommand
{
    protected string $name = 'View item';

    protected string $description = 'View a item';

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(
                SpotlightCommandDependency::make('item')
                    ->setPlaceholder('Which item do you want to view?')
            );
    }

    public function searchItem($query)
    {
        return Item::query()
            ->where('title', 'like', "%$query%")
            ->limit(10)
            ->get()
            ->map(function (Item $item) {
                return new SpotlightSearchResult(
                    $item->id,
                    $item->title,
                    sprintf('View item %s', $item->title)
                );
            });
    }

    public function execute(Item $item)
    {
        return redirect()->route('items.show', $item);
    }
}
