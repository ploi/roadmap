<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    public $fillable = [
        'title',
        'url',
        'description'
    ];

    public function boards()
    {
        return $this->hasMany(Board::class);
    }
}
