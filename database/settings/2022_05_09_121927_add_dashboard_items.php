<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddDashboardItems extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.dashboard_items', []);

        $this->migrator->add('general.welcome_text', 'Welcome to our roadmap!');
    }
}
