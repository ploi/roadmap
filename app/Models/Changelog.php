<?php

namespace App\Models;

use App\Traits\HasUpvote;
use App\Traits\Sluggable;
use App\Traits\HasOgImage;
use Database\Factories\ChangelogFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Changelog extends Model
{
    /** @use HasFactory<ChangelogFactory> */
    use HasFactory, Sluggable, HasOgImage, HasUpvote;

    public $fillable = [
        'slug',
        'title',
        'content',
        'published_at',
        'user_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Scope by published ones.
     *
     * @param Builder<Changelog> $query
     * @return Builder<Changelog>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published_at', '<=', now())->latest('published_at');
    }

    /**
     * Get the user that owns the changelog.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the item that owns the changelog.
     *
     * @return BelongsToMany<Item, $this>
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }
}
