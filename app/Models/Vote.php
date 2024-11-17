<?php

namespace App\Models;

use Database\Factories\VoteFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vote extends Model
{
    /** @use HasFactory<VoteFactory> */
    use HasFactory;

    public $fillable = [
        'subscribed'
    ];

    /**
     * Get the user that owns the vote.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function item(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * Scope by subscribed votes.
     *
     * @param Builder<Vote> $query
     * @return Builder<Vote>
     */
    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('subscribed', true);
    }
}
