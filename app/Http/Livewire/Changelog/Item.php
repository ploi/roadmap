<?php

namespace App\Http\Livewire\Changelog;

use App\Models\Changelog;
use Livewire\Component;

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
