<?php

namespace App\Models;

use App\Traits\HasOgImage;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

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

    protected $dates = [
        'published_at',
    ];

    public function scopePublished(Builder $query)
    {
        return $query->where('published_at', '<=', now())->latest();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }
}
