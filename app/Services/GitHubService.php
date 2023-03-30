<?php

namespace App\Services;

use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Support\Collection;

class GitHubService
{
    public function isEnabled(): bool
    {
        return filled(config('github.connections.main.token'));
    }

    public function getRepositories(): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        return collect(GitHub::me()->repositories('all'))->mapWithKeys(fn($repo) => [$repo['full_name'] => $repo['full_name']]);
    }

    public function getIssuesForRepository(string $repository): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        $repo = str($repository)->explode('/');

        return collect(GitHub::issues()->all($repo[0], $repo[1]))
            ->filter(fn($issue) => !isset($issue['pull_request']))
            ->mapWithKeys(fn($issue) => [$issue['id'] => '#' . $issue['number'] . ' - ' . $issue['title']]);
    }
}
