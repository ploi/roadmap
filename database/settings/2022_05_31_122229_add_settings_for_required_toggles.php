<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddSettingsForRequiredToggles extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.project_required_when_creating_item', false);
        $this->migrator->add('general.board_required_when_creating_item', false);
    }
}
