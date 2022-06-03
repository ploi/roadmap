<?php

namespace App\Spotlight;

use App\Models\Item;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightSearchResult;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightCommandDependencies;

class ViewItem extends SpotlightCommand
{
    public function getName(): string
    {
        return trans('spotlight.view-item.name');
    }

    public function getDescription(): string
    {
        return trans('spotlight.view-item.description');
    }

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
                    $item->slug,
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
