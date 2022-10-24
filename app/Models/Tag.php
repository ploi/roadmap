<?php

namespace App\Models;

class Tag extends \Spatie\Tags\Tag
{
    public function items()
    {
        return $this->morphedByMany(Item::class, 'taggable');
    }
}
