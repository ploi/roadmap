<?php

use App\Settings\GeneralSettings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
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
    }

    public function down()
    {
        //
    }
};
