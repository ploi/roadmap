<?php

namespace App\Livewire\Changelog;

use Livewire\Component;
use App\Models\Changelog;

class Item extends Component
{
    public Changelog $changelog;

    public function mount()
    {
        $this->items = $this->changelog->items;
    }

    public function render()
    {
        return view('livewire.changelog.item');
    }
}
