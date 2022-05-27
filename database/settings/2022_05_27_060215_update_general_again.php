<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class UpdateGeneralAgain extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.select_board_when_creating_item',  false);
        $this->migrator->add('general.select_project_when_creating_item',  false);
    }
}
