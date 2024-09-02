<?php

use function Pest\Laravel\artisan;

test('upgrade command works', function () {
    artisan('roadmap:upgrade')
        ->expectsOutput('Roadmap Upgrade')
        ->expectsOutputToContain('Clearing version data cache..')
        ->expectsOutputToContain('Version data cache has been cleared.')
        ->expectsOutputToContain('Caching routes..')
        ->expectsOutputToContain('Caching views..')
        ->expectsOutputToContain('Running migrations..')
        ->expectsOutputToContain('Upgrading done!')
        ->run();
})->only();
