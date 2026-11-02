<?php

use Illuminate\Support\Str;
use function Pest\Laravel\artisan;

use Illuminate\Support\Facades\File;

beforeEach(function () {
    // route:cache would otherwise clear and rewrite the shared bootstrap/cache file
    // while parallel test processes are booting from it.
    $this->routesCachePath = sys_get_temp_dir().'/roadmap-test-routes-'.Str::random(16).'.php';

    $_SERVER['APP_ROUTES_CACHE'] = $this->routesCachePath;
});

afterEach(function () {
    unset($_SERVER['APP_ROUTES_CACHE']);

    File::delete($this->routesCachePath);
});

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

    expect($this->routesCachePath)->toBeFile();
});
