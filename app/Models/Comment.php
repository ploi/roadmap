<?php

namespace App\Models;

use App\Traits\HasUpvote;
use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\LogOptions;
use Xetaio\Mentions\Models\Mention;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Xetaio\Mentions\Models\Traits\HasMentionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory, HasMentionsTrait, LogsActivity, HasUpvote;

    public $fillable = [
        'content',
        'parent_id',
        'user_id',
        'private',
    ];

    protected $casts = [
        'private' => 'boolean',
    ];

    /**
     * @var string[] $recordEvents
     */
    protected static array $recordEvents = ['updated'];

    /**
     * Get the user that owns the comment.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the item that owns the comment.
     *
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the parent comment.
     *
     * @return BelongsTo<Comment, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the child comments.
     *
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Get the comment mentions.
     *
     * @return MorphMany<Mention, $this>
     */
    public function mentions(): MorphMany
    {
        return $this->morphMany(Mention::class, 'model');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['content'])
            ->dontLogIfAttributesChangedOnly(['total_votes', 'updated_at'])
            ->logOnlyDirty();
    }

    /**
     * Scope by public comments.
     *
     * @param Builder<Comment> $query
     * @return Builder<Comment>
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('private', false);
    }
}
