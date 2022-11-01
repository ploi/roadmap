<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddExcludedWordsSetting extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.excluded_matching_search_words', [
            'the', 'it', 'that', 'when', 'how', 'this', 'true', 'false', 'is', 'not', 'well', 'with', 'use', 'enable', 'of', 'for', 'to'
        ]);
    }
}
