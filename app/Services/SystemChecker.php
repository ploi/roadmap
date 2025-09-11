<?php

namespace App\Services;

use Exception;

class SystemChecker
{
    public string|null $remoteVersion;
    public string|null $currentVersion;

    public string $cacheKeyCurrent = 'roadmap-current-version';
    public string $cacheKeyRemote = 'roadmap-remote-version';

    public function getVersions(): self
    {
        $this->remoteVersion = trim($this->getRemoteVersion());
        $this->currentVersion = trim($this->getApplicationVersion());

        return $this;
    }

    public function getApplicationVersion(): string|null
    {
        return cache()->remember($this->cacheKeyCurrent, now()->addDay(), function () {
            return shell_exec('git describe --tag --abbrev=0');
        });
    }

    public function getRemoteVersion(): string|null
    {
        return cache()->remember($this->cacheKeyRemote, now()->addDay(), function () {
            shell_exec('git fetch --tags');
            return shell_exec('git describe --tags $(git rev-list --tags --max-count=1)');
        });
    }

    public function isOutOfDate(): bool
    {
        $this->getVersions();

        return $this->currentVersion < $this->remoteVersion || $this->currentVersion != $this->remoteVersion;
    }

    public function flushVersionData(): void
    {
        try {
            cache()->forget($this->cacheKeyCurrent);
            cache()->forget($this->cacheKeyRemote);
        } catch (Exception $exception) {
        }
    }

    public function getPhpVersion(): string
    {
        return phpversion();
    }
}
