<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddDashboardItems extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.dashboard_items', [
            'recent-items',
            'recent-comments'
        ]);

        $this->migrator->add('general.welcome_text', 'Welcome to our roadmap!');
    }
}
