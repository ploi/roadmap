<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public bool $board_centered;

    public static function group(): string
    {
        return 'general';
    }
}
