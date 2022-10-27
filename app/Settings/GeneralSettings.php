<?php

namespace App\Settings;

use App\Enums\InboxWorkflow;
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
    public string|null $custom_scripts;
    public string|null $password;
    public bool $enable_item_age;
    public bool $show_voter_avatars;
    public bool $select_board_when_creating_item;
    public bool $select_project_when_creating_item;
    public bool $board_required_when_creating_item;
    public bool $project_required_when_creating_item;
    public bool $block_robots;
    public string $inbox_workflow;
    public bool $users_must_verify_email;
    public bool $enable_changelog;
    public bool $show_changelog_author;
    public bool $show_changelog_related_items;
    public bool $disable_file_uploads;
    public array $excluded_matching_search_words;

    public function getInboxWorkflow(): InboxWorkflow
    {
        return InboxWorkflow::from($this->inbox_workflow);
    }

    public function getDisabledToolbarButtons(): array
    {
        $toolbarButtons = [];

        if ($this->disable_file_uploads) {
            $toolbarButtons[] = 'attachFiles';
        }

        return $toolbarButtons;
    }

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
