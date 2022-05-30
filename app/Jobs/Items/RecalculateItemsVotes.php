<?php

namespace App\Jobs\Items;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RecalculateItemsVotes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Collection $itemIds)
    {
    }

    public function handle()
    {
        Item::query()
            ->whereIn('id', $this->itemIds->toArray())
            ->each(function (Item $item) {
                $item->total_votes = $item->votes()->count();
            });
    }
}
