<?php

use App\Settings\GeneralSettings;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $array = [];

        foreach (['Under review', 'Planned', 'In progress', 'Live', 'Closed'] as $defaultBoard) {
            $array[] = [
                'title'         => $defaultBoard,
                'visible'       => true,
                'sort_items_by' => 'popular',
            ];
        }

        app(GeneralSettings::class)->default_boards = $array;
        app(GeneralSettings::class)->save();
    }
};
