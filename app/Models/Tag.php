<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Tag extends \Spatie\Tags\Tag
{
    public function items()
    {
        return $this->morphedByMany(Item::class, 'taggable');
    }

    public function scopeForChangelog(Builder $query, Changelog $changelog): Builder
    {
        return $query
            ->where('changelog', '=', true)
            ->whereHas('items', function (Builder $query) use ($changelog) {
                return $query->whereHas('changelogs', function (Builder $query) use ($changelog) {
                    return $query->where('changelogs.id', $changelog->id);
                });
            });
    }
}
