<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddLeaderboardSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.enable_leaderboard', true);
        $this->migrator->add('general.leaderboard_users_count', 10);
    }
}
