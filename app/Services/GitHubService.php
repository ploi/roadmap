<?php

namespace App\Services;

use Github\Client;
use Github\ResultPager;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Support\Collection;
use Throwable;

class GitHubService
{
    public function getRepositories(?string $searchQuery = null): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        try {
            $gitHubClient = resolve('github.connection');
            $paginator = new ResultPager($gitHubClient);

            return collect($paginator->fetchAll($gitHubClient->api('me'), 'repositories', ['all']))
                ->filter(fn($repo) => str_contains($repo['full_name'], $searchQuery))
                ->mapWithKeys(fn($repo) => [$repo['full_name'] => $repo['full_name']]);
        } catch (Throwable $e) {
            logger()->error("Failed to retrieve GitHub repo's: {$e->getMessage()}");

            return collect();
        }
    }

    public function isEnabled(): bool
    {
        return config('github.enabled');
    }

    public function getIssuesForRepository(?string $repository, ?string $searchQuery = null): Collection
    {
        if (!$this->isEnabled() || $repository === null) {
            return collect();
        }

        $repo = str($repository)->explode('/');

        try {
            $gitHubClient = resolve('github.connection');
            $paginator = new ResultPager($gitHubClient);

            return collect($paginator->fetchAll($gitHubClient->api('issues'), 'all', [$repo[0], $repo[1]]))
                ->filter(fn($issue) => str_contains('#' . $issue['number'] . ' - ' . $issue['title'], $searchQuery))
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
