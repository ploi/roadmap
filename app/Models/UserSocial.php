<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\UserSocialFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSocial extends Model
{
    /** @use HasFactory<UserSocialFactory> */
    use HasFactory;

    public $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    public $guarded = [
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
