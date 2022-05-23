<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class UpdateGeneral extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->addEncrypted('general.password', '');
    }
}
