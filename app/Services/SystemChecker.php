<?php

namespace App\Services;

class SystemChecker
{
    public $remoteVersion;
    public $currentVersion;

    public function getVersions(): self
    {
        $this->remoteVersion = trim($this->getRemoteVersion());
        $this->currentVersion = trim($this->getApplicationVersion());

        return $this;
    }

    public function getApplicationVersion()
    {
        return cache()->remember('roadmap-current-version', now()->addDay(), function () {
            return shell_exec('git describe --tag --abbrev=0');
        });
    }

    public function getRemoteVersion()
    {
        return cache()->remember('roadmap-remote-version', now()->addDay(), function () {
            shell_exec('git fetch --tags');
            return shell_exec('git describe --tags $(git rev-list --tags --max-count=1)');
        });
    }

    public function isOutOfDate()
    {
        return $this->currentVersion < $this->remoteVersion || $this->currentVersion != $this->remoteVersion;
    }

    public function flushVersionData()
    {
        try {
            cache()->forget('roadmap-current-version');
            cache()->forget('roadmap-remote-version');
        } catch (\Exception $exception) {
        }
    }

    public function getPhpVersion(): string
    {
        return phpversion();
    }
}
