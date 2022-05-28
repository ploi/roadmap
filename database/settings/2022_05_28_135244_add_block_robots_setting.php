<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddBlockRobotsSetting extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.block_robots', false);
    }
}
