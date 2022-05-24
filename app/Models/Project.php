<?php

namespace App\Models;

use App\Traits\HasOgImage;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, Sluggable, HasOgImage;

    public $fillable = [
        'title',
        'slug',
        'url',
        'description'
    ];

    public function boards()
    {
        return $this->hasMany(Board::class)->orderBy('sort_order');
    }

    public function items()
    {
        return $this->hasManyThrough(Item::class, Board::class);
    }
}
