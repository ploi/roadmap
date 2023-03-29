<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ColorSettings extends Settings
{
    public string $primary;
    public string|null $favicon;
    public string|null $logo;
    public string|null $fontFamily;

    public static function group(): string
    {
        return 'colors';
    }
}
