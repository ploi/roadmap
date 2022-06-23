<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddToggleForVoterAvatarsInItemView extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.show_voter_avatars', false);
    }
}
