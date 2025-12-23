<?php

namespace App\Livewire\Item;

use App\Models\Item;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class VoteHistory extends Component
{
    public Item $item;

    public function render(): View
    {
        return view('livewire.item.vote-history');
    }
}
