<?php

namespace App\Models;

use App\Traits\Sluggable;
use App\Traits\HasOgImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Board extends Model
{
    use HasFactory, Sluggable, HasOgImage;

    const SORT_ITEMS_BY_POPULAR = 'popular';
    const SORT_ITEMS_BY_LATEST = 'latest';

    public $fillable = [
        'slug',
        'title',
        'visible',
        'description',
        'sort_order',
        'sort_items_by',
        'can_users_create',
    ];

    public $casts = [
        'visible' => 'boolean'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('visible', true);
    }

    public function canUsersCreateItem()
    {
        return $this->can_users_create;
    }
}
