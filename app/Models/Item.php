<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    public $fillable = [
        'title',
        'content'
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }
}
