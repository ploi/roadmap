<?php

namespace App\Observers;

use App\Models\Item;

class ItemObserver
{
    public function created(Item $item)
    {
        activity()
            ->performedOn($item)
            ->log('opened');
    }
}
