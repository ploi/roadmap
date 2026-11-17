<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebhookEndpoint extends Model
{
    use HasFactory;

    public $fillable = [
        'url',
        'secret',
        'events',
        'active',
    ];

    protected $hidden = ['secret'];

    protected function casts(): array
    {
        return [
            'secret' => 'encrypted',
            'events' => 'array',
            'active' => 'boolean',
        ];
    }
}
