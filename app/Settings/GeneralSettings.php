<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public bool $board_centered;
    public bool $create_default_boards;
    public bool $show_projects_sidebar_without_boards;
    public array $default_boards;
    public bool $allow_general_creation_of_item;
    public array $dashboard_items;
    public array $send_notifications_to;
    public string|null $welcome_text;
    public string|null $favicon;
    public $password;

    public static function group(): string
    {
        return 'general';
    }

    public static function encrypted(): array
    {
        return [
            'password'
        ];
    }
}
