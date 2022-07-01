<?php

namespace App\Services;

class SystemChecker
{
    public $remoteVersion;
    public $currentVersion;

    public string $cacheKeyCurrent = 'roadmap-current-version';
    public string $cacheKeyRemote = 'roadmap-remote-version';

    public function getVersions(): self
    {
        $this->remoteVersion = trim($this->getRemoteVersion());
        $this->currentVersion = trim($this->getApplicationVersion());

        return $this;
    }

    public function getApplicationVersion()
    {
        return cache()->remember($this->cacheKeyCurrent, now()->addDay(), function () {
            return shell_exec('git describe --tag --abbrev=0');
        });
    }

    public function getRemoteVersion()
    {
        return cache()->remember($this->cacheKeyRemote, now()->addDay(), function () {
            shell_exec('git fetch --tags');
            return shell_exec('git describe --tags $(git rev-list --tags --max-count=1)');
        });
    }

    public function isOutOfDate()
    {
        $this->getVersions();

        return $this->currentVersion < $this->remoteVersion || $this->currentVersion != $this->remoteVersion;
    }

    public function flushVersionData()
    {
        try {
            cache()->forget($this->cacheKeyCurrent);
            cache()->forget($this->cacheKeyRemote);
        } catch (\Exception $exception) {
        }
    }

    public function getPhpVersion(): string
    {
        return phpversion();
    }
}
