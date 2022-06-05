<?php

namespace App\Models;

use Xetaio\Mentions\Models\Mention;
use Illuminate\Database\Eloquent\Model;
use Xetaio\Mentions\Models\Traits\HasMentionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, HasMentionsTrait;

    public $fillable = [
        'content',
        'parent_id',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function mentions()
    {
        return $this->morphMany(Mention::class, 'model');
    }
}
