<?php

namespace App\Services;

use Throwable;
use Github\ResultPager;
use Illuminate\Support\Collection;
use GrahamCampbell\GitHub\Facades\GitHub;

class GitHubService
{
    /**
     * @param string $searchQuery
     * @return Collection<int|string, mixed>
     */
    public function getRepositories(string $searchQuery = ''): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        try {
            $gitHubClient = resolve('github.connection');
            $paginator = new ResultPager($gitHubClient);

            return collect($paginator->fetchAll($gitHubClient->api('me'), 'repositories', ['all']))
                ->filter(fn ($repo) => str_contains($repo['full_name'], $searchQuery))
                ->mapWithKeys(fn ($repo) => [$repo['full_name'] => $repo['full_name']]);
        } catch (Throwable $e) {
            logger()->error("Failed to retrieve GitHub repo's: {$e->getMessage()}");

            return collect();
        }
    }

    public function isEnabled(): bool
    {
        return (bool) config('github.enabled');
    }

    /**
     * @param string|null $repository
     * @param string $searchQuery
     * @return Collection<int|string, string>
     */
    public function getIssuesForRepository(?string $repository, string $searchQuery = ''): Collection
    {
        if (!$this->isEnabled() || $repository === null) {
            return collect();
        }

        $repo = str($repository)->explode('/');

        try {
            $gitHubClient = resolve('github.connection');
            $paginator = new ResultPager($gitHubClient);

            return collect($paginator->fetchAll($gitHubClient->api('issues'), 'all', [$repo[0], $repo[1]]))
                ->filter(fn ($issue) => str_contains('#' . $issue['number'] . ' - ' . $issue['title'], $searchQuery))
                ->filter(fn ($issue) => !isset($issue['pull_request']))
                ->mapWithKeys(fn ($issue) => [$issue['number'] => '#' . $issue['number'] . ' - ' . $issue['title']]);
        } catch (Throwable $e) {
            logger()->error("Failed to retrieve GitHub repo's: {$e->getMessage()}");

            return collect();
        }
    }

    public function getIssueTitle(?string $repository, ?int $issueNumber): ?string
    {
        if (!$this->isEnabled() || $repository === null || $issueNumber === null) {
            return null;
        }

        $repo = str($repository)->explode('/');

        try {
            $issue = GitHub::issues()->show($repo[0], $repo[1], $issueNumber);

            return "#{$issue['number']} - {$issue['title']}";
        } catch (Throwable $e) {
            logger()->error("Failed to retrieve GitHub issue #{$issueNumber}: {$e->getMessage()}");

            return null;
        }
    }

    public function createIssueInRepository(string $repository, string $title, string $body): int
    {
        $repo = str($repository)->explode('/');

        return GitHub::issues()->create($repo[0], $repo[1], [
            'title' => $title,
            'body'  => $body,
        ])['number'];
    }
}
