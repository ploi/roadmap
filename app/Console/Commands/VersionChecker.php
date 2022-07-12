<?php

namespace App\Console\Commands;

use App\Services\SystemChecker;
use Illuminate\Console\Command;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\RoadmapVersionOutOfDate;

class VersionChecker extends Command
{
    protected $signature = 'roadmap:version';

    protected $description = 'Send emails when the roadmap software is outdated';

    public function handle(SystemChecker $systemChecker): int
    {
        if (!$systemChecker->isOutOfDate()) {
            return 0;
        }

        if (!$receivers = app(GeneralSettings::class)->send_notifications_to) {
            return 0;
        }

        foreach ($receivers as $receiver) {
            Mail::to($receiver['email'])->send(new RoadmapVersionOutOfDate($receiver));
        }

        return 0;
    }
}
