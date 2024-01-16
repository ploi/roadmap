<?php

namespace App\Models;

use App\Traits\Sluggable;
use App\Traits\HasOgImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Changelog extends Model
{
    use HasFactory, Sluggable, HasOgImage;

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

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published_at', '<=', now())->latest('published_at');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }

    /**
     * Get the votes relationship.
     *
     * @return BelongsToMany
     */
    public function votes(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Check if a user has voted on this item.
     *
     * @param  User  $user  The user to check.
     *
     * @return bool Returns true if the user has voted on this item, otherwise false.
     */
    public function userVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }
}
