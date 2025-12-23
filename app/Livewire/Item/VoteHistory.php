<?php

namespace App\Livewire\Item;

use App\Models\Item;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Contracts\View\View;

class VoteHistory extends Component
{
    public Item $item;

    public bool $modalOpened = false;

    #[On('open-modal')]
    public function onModalOpen(string $id): void
    {
        if ($id === 'vote-history-modal') {
            $this->modalOpened = true;
        }
    }

    public function render(): View
    {
        return view('livewire.item.vote-history');
    }
}
