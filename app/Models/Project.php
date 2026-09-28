<?php

namespace App\Models;

use App\Traits\Sluggable;
use App\Traits\HasOgImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory, Sluggable, HasOgImage;

    public $fillable = [
        'title',
        'slug',
        'group',
        'icon',
        'icon_image',
        'url',
        'description',
        'repo',
        'private',
        'collapsible',
        'sort_order',
    ];

    protected $casts = [
        'private' => 'boolean',
        'collapsible' => 'boolean',
    ];

    public function boards()
    {
        return $this->hasMany(Board::class)->orderBy('sort_order');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_member')->using(ProjectMember::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get the public URL of the uploaded icon image, if one is set.
     */
    public function iconImageUrl(): ?string
    {
        if (! $this->icon_image) {
            return null;
        }

        return Storage::disk('public')->url($this->icon_image);
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
