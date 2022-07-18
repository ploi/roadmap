<?php

use Mockery\MockInterface;
use App\Services\SystemChecker;
use App\Settings\GeneralSettings;
use function Pest\Laravel\artisan;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\RoadmapVersionOutOfDate;

beforeEach(function () {
    Mail::fake();
    GeneralSettings::fake(['send_notifications_to' => [['name' => 'Ploi.io', 'email' => 'info@ploi.io']]]);
});

test('version command send emails if version is out of date', function () {
    $this->mock(SystemChecker::class, function (MockInterface $mock) {
        $mock->shouldReceive('isOutOfDate')->once()->andReturn(true);
    });

    artisan('roadmap:version')->run();

    Mail::assertQueued(RoadmapVersionOutOfDate::class);
});

test('version command wont send emails if version is up to date', function () {
    $this->mock(SystemChecker::class, function (MockInterface $mock) {
        $mock->shouldReceive('isOutOfDate')->once()->andReturn(false);
    });

    artisan('roadmap:version')->run();

    Mail::assertNotQueued(RoadmapVersionOutOfDate::class);
});
