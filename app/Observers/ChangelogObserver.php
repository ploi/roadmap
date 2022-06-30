<?php

namespace App\Observers;

use App\Models\Changelog;

class ChangelogObserver
{
    public function deleting(Changelog $changelog)
    {
        $changelog->items()->detach();
    }
}
