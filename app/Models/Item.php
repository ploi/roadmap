<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

    const STATUS_OPEN = 'open';
    const STATUS_REVIEW = 'under-review';
    const STATUS_PLANNED = 'planned';
    const STATUS_LIVE = 'live';

    public $fillable = [
        'title',
        'content'
    ];

    protected function excerpt(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return Str::limit(strip_tags(str($this->attributes['content'])->markdown()), 150);
            },
        );
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
