<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
        'username',
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

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'model');
    }

    public function votedItems()
    {
        return $this->hasManyThrough(Item::class, Vote::class, 'user_id', 'items.id', 'id', 'item_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function assignedItems()
    {
        return $this->belongsToMany(Item::class, 'item_user');
    }

    public static function booted()
    {
        static::creating(function (self $user) {
            $user->username = Str::slug($user->name);
        });

        static::updating(function (self $user) {
            $user->username = Str::lower($user->username);
        });
    }
}
