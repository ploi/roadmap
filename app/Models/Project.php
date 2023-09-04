<?php

namespace App\Models;

use App\Traits\Sluggable;
use App\Traits\HasOgImage;
use App\Traits\HasScopeChecks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory, Sluggable, HasOgImage, HasScopeChecks;

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

    public function boards()
    {
        return $this->hasMany(Board::class)->orderBy('sort_order');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_member')->using(ProjectMember::class);
    }

    public function items()
    {
        return $this->hasManyThrough(Item::class, Board::class);
    }

    public function scopeVisibleForCurrentUser($query)
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
