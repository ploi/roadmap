<?php

namespace App\Services;

use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Support\Collection;
use Throwable;

class GitHubService
{
    public function getRepositories(): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        try {
            return collect(GitHub::me()->repositories('all'))->mapWithKeys(fn($repo
            ) => [$repo['full_name'] => $repo['full_name']]);
        } catch (Throwable $e) {
            logger()->error("Failed to retrieve GitHub repo's: {$e->getMessage()}");

            return collect();
        }
    }

    public function isEnabled(): bool
    {
        return filled(config('github.connections.main.token'));
    }

    public function getIssuesForRepository(string $repository): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        $repo = str($repository)->explode('/');

        try {
            return collect(GitHub::issues()->all($repo[0], $repo[1]))
                ->filter(fn($issue) => !isset($issue['pull_request']))
                ->mapWithKeys(fn($issue) => [$issue['number'] => '#' . $issue['number'] . ' - ' . $issue['title']]);
        } catch (Throwable $e) {
            logger()->error("Failed to retrieve GitHub repo's: {$e->getMessage()}");

            return collect();
        }
    }

    public function createIssueInRepository(string $repository, $title, $body): int
    {
        $repo = str($repository)->explode('/');

        return GitHub::issues()->create($repo[0], $repo[1], [
            'title' => $title,
            'body'  => $body,
        ])['number'];
    }
}
