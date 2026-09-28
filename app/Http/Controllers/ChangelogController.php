<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Changelog;
use Illuminate\Http\Request;
use App\Settings\GeneralSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ChangelogController extends Controller
{
    /**
     * The value of the project filter that selects changelogs without a project.
     */
    public const WITHOUT_PROJECT_FILTER = 'none';

    public function index(Request $request): View
    {
        abort_unless(app(GeneralSettings::class)->enable_changelog, 404);

        $isFilteringWithoutProject = $request->query('project') === self::WITHOUT_PROJECT_FILTER;

        return $this->overview($request, $isFilteringWithoutProject ? self::WITHOUT_PROJECT_FILTER : null);
    }

    /**
     * Show a single changelog, or the changelogs of a project when the slug belongs to a project instead.
     */
    public function show(Request $request, string $slug): View
    {
        abort_unless(app(GeneralSettings::class)->enable_changelog, 404);

        $changelog = Changelog::query()->where('slug', $slug)->first();

        if (! $changelog) {
            return $this->overview($request, $slug);
        }

        abort_if(! $changelog->published_at || $changelog->published_at->isFuture(), 404);

        return view('changelog', [
            'changelog' => $changelog,
            'selectedProject' => $changelog->project?->isVisibleForCurrentUser() ? $changelog->project : null,
        ]);
    }

    /**
     * The URL of the changelog overview for a project slug or the without project filter.
     */
    public static function overviewUrl(?string $projectFilter = null, int $page = 1): string
    {
        $query = array_filter(['page' => $page > 1 ? $page : null]);

        return match ($projectFilter) {
            null => route('changelog', $query),
            self::WITHOUT_PROJECT_FILTER => route('changelog', ['project' => $projectFilter, ...$query]),
            default => route('changelog.show', ['slug' => $projectFilter, ...$query]),
        };
    }

    protected function overview(Request $request, ?string $projectFilter): View
    {
        $filterProjects = Project::query()
            ->where(fn (Builder $query) => $query->visibleForCurrentUser())
            ->whereHas('changelogs', fn (Builder $query) => $query->published())
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $hasChangelogsWithoutProject = $filterProjects->isNotEmpty()
            && Changelog::query()->published()->whereNull('project_id')->exists();

        $selectedProject = null;

        if ($projectFilter !== null && $projectFilter !== self::WITHOUT_PROJECT_FILTER) {
            $selectedProject = $filterProjects->firstWhere('slug', $projectFilter);

            abort_unless($selectedProject, 404);
        }

        return view('changelog', [
            'filterProjects' => $filterProjects,
            'hasChangelogsWithoutProject' => $hasChangelogsWithoutProject,
            'projectFilter' => $projectFilter,
            'selectedProject' => $selectedProject,
            'page' => max(1, $request->integer('page', 1)),
        ]);
    }
}
