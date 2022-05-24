<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        // General
        $this->migrator->add('general.board_centered', false);
        $this->migrator->add('general.create_default_boards', true);
        $this->migrator->add('general.default_boards', [
            'Under review', 'Planned', 'In progress', 'Live', 'Closed'
        ]);
        $this->migrator->add('general.show_projects_sidebar_without_boards', true);
        $this->migrator->add('general.allow_general_creation_of_item', true);
        $this->migrator->add('general.dashboard_items', []);
        $this->migrator->add('general.welcome_text', 'Welcome to our roadmap!');
        $this->migrator->add('general.send_notifications_to', []);

        // Colors
        $this->migrator->add('colors.primary', '#2563EB');
    }
}
