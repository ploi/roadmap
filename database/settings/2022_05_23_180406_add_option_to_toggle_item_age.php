<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddOptionToToggleItemAge extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.enable_item_age', false);
    }
}
