<?php

use function Pest\Laravel\artisan;

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
