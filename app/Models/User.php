<?php

namespace App\Models;

use Filament\Panel;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Settings\GeneralSettings;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Database\Factories\UserSocialFactory;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    /** @use HasFactory<UserSocialFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'role',
        'email',
        'username',
        'password',
        'notification_settings',
        'per_page_setting',
        'locale',
        'date_locale',
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

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAdminAccess();
    }

    public function hasAdminAccess(): bool
    {
        return in_array($this->role, [UserRole::Admin, UserRole::Employee]);
    }

    public function canImpersonate(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function hasRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function getGravatar(int $size = 150): string
    {
        $email = is_string(Arr::get($this->attributes, 'email')) ? trim(Arr::get($this->attributes, 'email')) : '';

        return sprintf(
            '%s/%s?s=%d',
            config()->string('services.gravatar.base_url'),
            md5(strtolower(trim($email))),
            (int)$size
        );
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getGravatar();
    }

    /**
     * Get the user's items.
     *
     * @return HasMany<Item, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get the projects that user belongs.
     *
     * @return BelongsToMany<Project, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_member')->using(ProjectMember::class);
    }

    /**
     * Get the user's votes.
     *
     * @return HasMany<Vote, $this>
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Get the user's voted items.
     *
     * @return HasManyThrough<Item, Vote, $this>
     */
    public function votedItems(): HasManyThrough
    {
        return $this->hasManyThrough(Item::class, Vote::class, 'user_id', 'items.id', 'id', 'model_id');
    }

    /**
     * Get the user's comments.
     *
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the items assigned to the user.
     *
     * @return BelongsToMany<Item, $this>
     */
    public function assignedItems(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_user');
    }

    /**
     * @return HasManyThrough<Item, Comment, $this>
     */
    public function commentedItems(): hasManyThrough
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

    /**
     * Get the user's mentions.
     *
     * @return MorphToMany<Comment, $this>
     */
    public function mentions(): MorphToMany
    {
        return $this
            ->morphedByMany(Comment::class, 'model', 'mentions', 'recipient_id')
            ->where('recipient_type', User::class);
    }

    /**
     * Get the user's socials.
     *
     * @return HasMany<UserSocial, $this>
     */
    public function userSocials(): hasMany
    {
        return $this->hasMany(UserSocial::class);
    }

    public function wantsNotification(string $type): bool
    {
        return in_array($type, $this->notification_settings ?? []);
    }

    public function needsToVerifyEmail() : bool
    {
        return app(GeneralSettings::class)->users_must_verify_email &&
             !auth()->user()?->hasVerifiedEmail();
    }

    public function isSubscribedToItem(Item $item): bool
    {
        return $item->subscribedVotes()->where('user_id', $this->id)->exists();
    }

    public function toggleVoteSubscription(int $id, string $type): void
    {
        $vote = Vote::where('model_id', $id)
            ->where('model_type', $type)
            ->where('user_id', $this->id)
            ->first();

        if (!$vote) {
            return;
        }

        $vote->update(['subscribed' => !$vote->subscribed]);
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
            $user->username = Str::lower((string) $user->username);
        });
    }
}
