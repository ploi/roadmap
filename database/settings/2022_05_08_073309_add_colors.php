<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddColors extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('colors.primary', '#3b82f6');
    }
}
