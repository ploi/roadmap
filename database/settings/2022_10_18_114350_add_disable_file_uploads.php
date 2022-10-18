<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddDisableFileUploads extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.disable_file_uploads', false);
    }
}
