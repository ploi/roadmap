<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddCustomScriptsSetting extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.custom_scripts');
    }
}
