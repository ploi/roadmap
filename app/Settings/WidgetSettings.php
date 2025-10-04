<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class WidgetSettings extends Settings
{
    public bool $enabled;
    public string $position;
    public array $allowed_domains;
    public string $primary_color;
    public string $button_text;

    public static function group(): string
    {
        return 'widget';
    }
}
