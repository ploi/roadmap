<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddGitHub extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.show_github_link', false);
    }
}
