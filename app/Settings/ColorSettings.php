<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ColorSettings extends Settings
{
    public string $primary;

    public static function group(): string
    {
        return 'colors';
    }
}
