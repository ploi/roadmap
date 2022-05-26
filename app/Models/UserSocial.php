<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSocial extends Model
{
    use HasFactory;

    public $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    public $guarded = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
