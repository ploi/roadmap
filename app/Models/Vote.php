<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Vote extends Model
{
    use HasFactory;

    public $fillable = [
        'subscribed'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): MorphTo
    {
        return $this->morphTo('model');
    }
}
