<?php

use App\Settings\GeneralSettings;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

class ConvertDefaultBoardsSetting extends SettingsMigration
{
    public function up(): void
    {
        $array = [];

        foreach (app(GeneralSettings::class)->default_boards as $defaultBoard) {
            $array[] = [
                'title'         => $defaultBoard,
                'visible'       => true,
                'sort_items_by' => 'popular',
            ];
        }

        app(GeneralSettings::class)->default_boards = $array;
        app(GeneralSettings::class)->save();
    }
}
