<?php

namespace App\Models;

use App\Traits\Sluggable;
use App\Traits\HasUpvote;
use App\Traits\HasOgImage;
use Illuminate\Support\Str;
use App\Enums\InboxWorkflow;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory, Sluggable, HasOgImage, HasUpvote;

    public $fillable = [
        'slug',
        'title',
        'content',
        'pinned',
        'private',
        'notify_subscribers',
        'project_id',
        'board_id',
        'user_id'
    ];

    protected $casts = [
        'pinned' => 'boolean',
        'private' => 'boolean',
        'notify_subscribers' => 'boolean',
    ];

    protected function excerpt(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return Str::limit(strip_tags(str($this->attributes['content'])->markdown()->trim()), 150);
            },
        );
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function subscribedVotes(): MorphMany
    {
        return $this->votes()->where('subscribed', true);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'item_user');
    }

    public function changelogs(): BelongsToMany
    {
        return $this->belongsToMany(Changelog::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'subject');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('total_votes', 'desc');
    }

    public function scopeVisibleForCurrentUser(Builder $query)
    {
        if (auth()->user()?->hasAdminAccess()) {
            return $query;
        }

        return $query->where('private', 0)->where(function (Builder $query) {
            return $query->whereRelation('project', 'private', 0)->orWhereNull('items.project_id');
        });
    }

    public function scopeForInbox($query)
    {
        return match (app(GeneralSettings::class)->getInboxWorkflow()) {
            InboxWorkflow::WithoutBoardAndProject => $query->whereNull('project_id')->whereNull('board_id'),
            InboxWorkflow::WithoutBoardOrProject => $query->where(fn ($query) => $query->orWhereNull('project_id')->orWhereNull('board_id')),
            InboxWorkflow::WithoutBoard => $query->whereNotNull('project_id')->whereNull('board_id'),
            InboxWorkflow::Disabled => null,
        };
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
