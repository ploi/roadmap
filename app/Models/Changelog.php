<?php

namespace App\Models;

use App\Traits\HasUpvote;
use App\Traits\Sluggable;
use App\Traits\HasOgImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Changelog extends Model
{
    use HasFactory, Sluggable, HasOgImage, HasUpvote;

    public $fillable = [
        'slug',
        'title',
        'content',
        'published_at',
        'user_id',
        'project_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published_at', '<=', now())->latest('published_at')->latest('id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }

    /**
     * The markdown content rendered to sanitized HTML.
     */
    protected function contentHtml(): Attribute
    {
        return Attribute::get(fn (): string => (string) str($this->content)->markdown()->sanitizeHtml())->shouldCache();
    }

    /**
     * The first paragraph of the rendered content, or the full content when it has no paragraph.
     */
    protected function excerptHtml(): Attribute
    {
        return Attribute::get(function (): string {
            if (preg_match('/<p>.*?<\/p>/s', $this->content_html, $matches)) {
                return $matches[0];
            }

            return $this->content_html;
        })->shouldCache();
    }

    public function hasMoreContentThanExcerpt(): bool
    {
        return trim($this->excerpt_html) !== trim($this->content_html);
    }
}
