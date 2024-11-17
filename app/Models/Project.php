<?php

namespace App\Models;

use App\Traits\Sluggable;
use App\Traits\HasOgImage;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, Sluggable, HasOgImage;

    public $fillable = [
        'title',
        'slug',
        'group',
        'icon',
        'url',
        'description',
        'repo',
        'private',
        'sort_order',
    ];

    protected $casts = [
        'private' => 'boolean',
    ];

    /**
     * Get the boards for the project.
     *
     * @return HasMany<Board, $this>
     */
    public function boards(): HasMany
    {
        return $this->hasMany(Board::class)->orderBy('sort_order');
    }

    /**
     * Get the project members.
     *
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_member')->using(ProjectMember::class);
    }

    /**
     * Get the project items.
     *
     * @return HasMany<Item, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Scope by visible projects to current user.
     *
     * @param Builder<Project> $query
     * @return Builder<Project>
     */
    public function scopeVisibleForCurrentUser(Builder $query): Builder
    {
        if (auth()->user()?->hasAdminAccess()) {
            return $query;
        }

        if (auth()->check()) {
            return $query
                ->whereHas('members', fn (Builder $query) => $query->where('user_id', auth()->id()))
                ->orWhere('private', false);
        }

        return $query->where('private', false);
    }
}
