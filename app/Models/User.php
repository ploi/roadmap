<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Settings\GeneralSettings;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'role',
        'email',
        'username',
        'password',
        'notification_settings',
        'per_page_setting'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'notification_settings' => 'array',
        'per_page_setting' => 'array',
        'role' => UserRole::class,
    ];

    public function canAccessFilament(): bool
    {
        return $this->hasAdminAccess();
    }

    public function hasAdminAccess(): bool
    {
        return in_array($this->role, [UserRole::Admin, UserRole::Employee]);
    }

    public function hasRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function getGravatar($size = 150): string
    {
        return sprintf(
            '%s/%s?s=%d',
            config('services.gravatar.base_url'),
            md5(strtolower(trim(Arr::get($this->attributes, 'email')))),
            (int)$size
        );
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

    public function commentedItems()
    {
        return $this->hasManyThrough(
            Item::class,
            Comment::class,
            'user_id',
            'id',
            'id',
            'item_id'
        )->withMax('comments', 'created_at')->distinct('comments.item_id');
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

    public function needsToVerifyEmail() : bool
    {
        return app(GeneralSettings::class)->users_must_verify_email &&
             !auth()->user()->hasVerifiedEmail();
    }

    public static function booted()
    {
        static::creating(function (self $user) {
            $user->username = Str::slug($user->name);
            $user->notification_settings = [
                'receive_mention_notifications',
                'receive_comment_reply_notifications',
            ];
            $user->per_page_setting = ['5','15','25'];
        });

        static::updating(function (self $user) {
            $user->username = Str::lower($user->username);
        });
    }
}
