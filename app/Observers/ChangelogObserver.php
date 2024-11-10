<?php

namespace App\Observers;

use App\Models\Changelog;

class ChangelogObserver
{
    public function deleting(Changelog $changelog): void
    {
        $changelog->items()->detach();
    }
}
