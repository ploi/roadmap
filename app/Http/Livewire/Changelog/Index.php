<?php

namespace App\Http\Livewire\Changelog;

use Livewire\Component;

class Index extends Component
{
    public $changelogs = [];

    protected $listeners = [
        'item-created' => '$refresh',
    ];

    public function render()
    {
        return view('livewire.changelog.index');
    }
}
