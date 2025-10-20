<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ActivityWidgetSettings extends Settings
{
    public bool $enabled;
    public string $position;
    public array $allowed_domains;
    public string $primary_color;
    public string $button_text;
    public bool $hide_button;
    public string $modal_title;
    public int $items_limit;

    public static function group(): string
    {
        return 'activity_widget';
    }
}
