<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddUserVerifiedSetting extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.users_must_verify_email', false);
    }
}
