<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiToken extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'token',
        'last_used_at',
    ];

    protected $hidden = ['token'];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * Only a hash is stored, so the returned plain text token can be shown once and never again.
     *
     * @return array{0: self, 1: string}
     */
    public static function generate(string $name): array
    {
        $plainTextToken = 'rm_' . Str::random(40);

        return [
            static::create(['name' => $name, 'token' => static::hash($plainTextToken)]),
            $plainTextToken,
        ];
    }

    public static function findByPlainTextToken(string $plainTextToken): ?self
    {
        return static::where('token', static::hash($plainTextToken))->first();
    }

    private static function hash(string $plainTextToken): string
    {
        return hash('sha256', $plainTextToken);
    }
}
