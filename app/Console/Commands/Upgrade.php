<?php

namespace App\Console\Commands;

use App\Services\SystemChecker;
use Illuminate\Console\Command;
use App\Console\Commands\Concerns\CanShowAnIntro;

class Upgrade extends Command
{
    use CanShowAnIntro;

    protected $signature = 'roadmap:upgrade';

    protected $description = 'Used inside your deployment process to update all variables.';

    public function handle(): void
    {
        $this->intro(type: 'upgrade');

        $this->flushVersionData();
        $this->line(' ');
        $this->cacheRoutes();
        $this->line(' ');
        $this->cacheViews();
        $this->line(' ');
        $this->migrateMigrations();
        $this->publishAssets();
        $this->line(' ');

        $this->info('Upgrading done!');
    }

    protected function flushVersionData(): void
    {
        $this->info('Clearing version data cache..');

        (new SystemChecker)->flushVersionData();

        $this->info('Version data cache has been cleared.');
    }

    protected function migrateMigrations(): void
    {
        $this->info('Running migrations..');

        $this->call('migrate', ['--force' => true]);
    }

    protected function cacheRoutes(): void
    {
        $this->info('Caching routes..');

        $this->call('route:cache');
    }

    protected function cacheViews(): void
    {
        $this->info('Caching views..');

        $this->call('view:cache');
    }

    protected function publishAssets(): void
    {
        $this->info('Publishing assets..');

        $this->call('filament:assets');
    }
}
