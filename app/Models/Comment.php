<?php

namespace App\Models;

use App\Traits\HasUpvote;
use Spatie\Activitylog\LogOptions;
use Xetaio\Mentions\Models\Mention;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Xetaio\Mentions\Models\Traits\HasMentionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, HasMentionsTrait, LogsActivity, HasUpvote;

    public $fillable = [
        'content',
        'parent_id',
        'user_id',
        'private',
    ];

    protected $casts = [
        'private' => 'boolean',
    ];

    protected static $recordEvents = ['updated'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function mentions()
    {
        return $this->morphMany(Mention::class, 'model');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['content'])
            ->dontLogIfAttributesChangedOnly(['total_votes', 'updated_at'])
            ->logOnlyDirty();
    }

    public function scopePublic($query)
    {
        return $query->where('private', false);
    }
}
