<?php

namespace App\Models;

use Spatie\Tags\HasTags;
use App\Traits\HasUpvote;
use App\Traits\Sluggable;
use App\Traits\HasOgImage;
use Illuminate\Support\Str;
use App\Enums\InboxWorkflow;
use App\Settings\GeneralSettings;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Activitylog\Exceptions\InvalidConfiguration;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory, Sluggable, HasOgImage, HasUpvote, HasTags;

    public $fillable = [
        'slug',
        'title',
        'content',
        'pinned',
        'private',
        'notify_subscribers',
        'project_id',
        'board_id',
        'user_id',
        'issue_number'
    ];

    protected $casts = [
        'pinned' => 'boolean',
        'private' => 'boolean',
        'notify_subscribers' => 'boolean',
    ];

    public static function getTagClassName(): string
    {
        return Tag::class;
    }

    /**
     * @return Attribute<string, string>
     */
    protected function excerpt(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return Str::limit(strip_tags(str($this->attributes['content'])->markdown()->trim()), 150);
            },
        );
    }

    /**
     * @return Attribute<string, string>
     */
    protected function viewUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if ($this->project) {
                    return route('projects.items.show', [$this->project, $attributes['slug']]);
                }

                return route('items.show', [$attributes['slug']]);
            },
        )->shouldCache();
    }

    /**
     * Get the board that owns the item..
     *
     * @return BelongsTo<Board, $this>
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Get the user that owns the item.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project that owns the item.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return MorphMany<Vote, $this>
     */
    public function subscribedVotes(): MorphMany
    {
        return $this->votes()->where('subscribed', true);
    }

    /**
     * Get the comments for the item.
     *
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the parent comments for the item.
     *
     * @return HasMany<Comment, $this>
     */
    public function parentComments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNotNull('parent_id')->latest();
    }

    /**
     * Get the assigned users that owns the item.
     *
     * @return BelongsToMany<User, $this>
     */
    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'item_user');
    }

    /**
     * @return BelongsToMany<Changelog, $this>
     */
    public function changelogs(): BelongsToMany
    {
        return $this->belongsToMany(Changelog::class);
    }

    /**
     * @return MorphMany<Model, $this>
     * @throws InvalidConfiguration
     */
    public function activities(): MorphMany
    {
        return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'subject');
    }

    /**
     * @return MorphToMany<Tag, $this>
     */
    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany(Tag::class, 'taggable', 'taggables', null, 'tag_id')
            ->orderBy('order_column');
    }

    /**
     * @param Builder<Item> $query
     * @return Builder<Item>
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('total_votes', 'desc');
    }

    /**
     * @param Builder<Item> $query
     * @return Builder<Item>
     */
    public function scopeVisibleForCurrentUser(Builder $query): Builder
    {
        if (auth()->user()?->hasAdminAccess()) {
            return $query;
        }

        return $query->where('private', 0)->where(function (Builder $query) {
            return $query->whereRelation('project', 'private', 0)->orWhereNull('items.project_id');
        });
    }

    /**
     * @param Builder<Item> $query
     * @return Builder<Item>|null
     */
    public function scopeForInbox(Builder $query): Builder|null
    {
        return match (app(GeneralSettings::class)->getInboxWorkflow()) {
            InboxWorkflow::WithoutBoardAndProject => $query->whereNull('project_id')->whereNull('board_id'),
            InboxWorkflow::WithoutBoardOrProject => $query->where(fn ($query) => $query->orWhereNull('project_id')->orWhereNull('board_id')),
            InboxWorkflow::WithoutBoard => $query->whereNotNull('project_id')->whereNull('board_id'),
            InboxWorkflow::Disabled => null,
        };
    }

    /**
     * @param Builder<Item> $query
     * @return Builder<Item>
     */
    public function scopeNoChangelogTag(Builder $query): Builder
    {
        return $query
            ->whereDoesntHave('tags', function (Builder $query) {
                return $query->where('changelog', '=', true);
            });
    }

    public function isPinned(): bool
    {
        return $this->pinned;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }
}
