<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
