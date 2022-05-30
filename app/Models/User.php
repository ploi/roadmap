<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'notification_settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'notification_settings' => 'array'
    ];

    public function canAccessFilament(): bool
    {
        return $this->admin;
    }

    public function getGravatar($size = 150)
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim(Arr::get($this->attributes, 'email')))) . '?s=' . (int)$size;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getGravatar();
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function votedItems()
    {
        return $this->hasManyThrough(Item::class, Vote::class, 'user_id', 'items.id', 'id', 'model_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function assignedItems()
    {
        return $this->belongsToMany(Item::class, 'item_user');
    }

    public function mentions()
    {
        return $this
            ->morphedByMany(Comment::class, 'model', 'mentions', 'recipient_id')
            ->where('recipient_type', User::class);
    }

    public function userSocials()
    {
        return $this->hasMany(UserSocial::class);
    }

    public function wantsNotification($type)
    {
        return in_array($type, $this->notification_settings ?? []);
    }

    public static function booted()
    {
        static::creating(function (self $user) {
            $user->username = Str::slug($user->name);
            $user->notification_settings = [
                'receive_mention_notifications'
            ];
        });

        static::updating(function (self $user) {
            $user->username = Str::lower($user->username);
        });
    }
}
