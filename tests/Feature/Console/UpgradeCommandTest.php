<?php

use App\Models\User;
use App\Enums\UserRole;
use function Pest\Laravel\artisan;
use function PHPUnit\Framework\assertSame;
use function Pest\Laravel\assertDatabaseCount;

test('upgrade command works', function () {
    artisan('roadmap:upgrade')
        ->expectsOutput('Roadmap Upgrade')
        ->expectsOutput('Clearing version data cache..')
        ->expectsOutput('Version data cache has been cleared.')
        ->expectsOutput('Caching routes..')
        ->expectsOutput('Caching views..')
        ->expectsOutput('Running migrations..')
        ->expectsOutput('Upgrading done!')
        ->run();
});
