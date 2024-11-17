<?php

namespace App\Models;

use App\Traits\Sluggable;
use App\Traits\HasOgImage;
use Database\Factories\BoardFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    /** @use HasFactory<BoardFactory> */
    use HasFactory, Sluggable, HasOgImage;

    const SORT_ITEMS_BY_POPULAR = 'popular';
    const SORT_ITEMS_BY_LATEST = 'latest';

    public $fillable = [
        'slug',
        'title',
        'visible',
        'sort_order',
        'description',
        'block_votes',
        'sort_items_by',
        'block_comments',
        'can_users_create',
    ];

    public $casts = [
        'visible' => 'boolean',
        'can_users_create' => 'boolean',
        'block_comments' => 'boolean',
        'block_votes' => 'boolean'
    ];

    /**
     * Get the project that owns the board.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the items for the board.
     *
     * @return HasMany<Item, $this>
     */
    public function items(): hasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Scope by visible boards.
     *
     * @param Builder<Board> $query
     * @return Builder<Board>
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visible', true);
    }

    public function canUsersCreateItem(): bool
    {
        return $this->can_users_create;
    }
}
