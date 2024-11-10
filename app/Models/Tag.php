<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasTranslations;

    /**
     * @return MorphToMany<Item, $this>
     */
    public function items(): MorphToMany
    {
        return $this->morphedByMany(Item::class, 'taggable');
    }

    /**
     * @param Builder<Tag> $query
     * @param Changelog $changelog
     * @return Builder<Tag>
     */
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
