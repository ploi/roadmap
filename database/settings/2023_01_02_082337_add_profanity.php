<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddProfanity extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.profanity_words', [
            'fuck', 'asshole', 'dick', 'screw you'
        ]);
    }
}
