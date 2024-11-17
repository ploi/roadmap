<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Settings\GeneralSettings;

class ConvertNotifications extends Command
{
    protected $signature = 'roadmap:convert-notifications';

    protected $description = 'Convert notifications to new format';

    public function handle(): int
    {
        $array = [];

        foreach (app(GeneralSettings::class)->send_notifications_to as $sendNotificationsTo) {
            $array[] = [
                'name' => $sendNotificationsTo['name'],
                'webhook' => $sendNotificationsTo['email'],
                'type' => 'email'
            ];
        }

        app(GeneralSettings::class)->send_notifications_to = $array;
        app(GeneralSettings::class)->save();

        return Command::SUCCESS;
    }
}
