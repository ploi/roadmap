<?php

namespace App\Spotlight;

use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;

class CreateItem extends SpotlightCommand
{
    public function getName(): string
    {
        return trans('spotlight.create-item.name');
    }

    public function getDescription(): string
    {
        return trans('spotlight.create-item.description');
    }

    public function execute(Spotlight $spotlight)
    {
        $spotlight->emit('openModal', 'modals.item.create-item-modal');
    }
}
