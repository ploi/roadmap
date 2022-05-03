<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class UpdateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.create_default_boards', false);
        $this->migrator->add('general.default_boards', []);
    }
}
