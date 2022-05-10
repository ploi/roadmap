<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function canAccessFilament(): bool
    {
        return $this->admin;
    }

    public function getGravatar($size = 150)
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim(Arr::get($this->attributes, 'email')))) . '?s=' . (int)$size;
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function votedItems()
    {
        return $this->hasManyThrough(Item::class, Vote::class, 'user_id', 'id', 'id', 'item_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
