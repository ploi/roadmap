<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class ChangelogSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.enable_changelog', false);
        $this->migrator->add('general.show_changelog_author', true);
        $this->migrator->add('general.show_changelog_related_items', true);
    }
}
