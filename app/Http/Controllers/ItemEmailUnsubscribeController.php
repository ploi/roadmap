<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ItemEmailUnsubscribeController extends Controller
{
    public function __invoke(Item $item, User $user): RedirectResponse
    {
        if (!$user->isSubscribedToItem($item)) {
            return redirect()->route('home');
        }

        $user->toggleVoteSubscription($item->id, Item::class);

        return redirect()->route('items.show', $item->getAttributeValue('slug'));
    }
}
