<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public bool $board_centered;

    public bool $create_default_boards;

    public array $default_boards;

    public static function group(): string
    {
        return 'general';
    }
}
