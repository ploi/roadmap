<?php

namespace App\Livewire\Changelog;

use Livewire\Component;
use App\Models\Changelog;
use App\Settings\GeneralSettings;
use Livewire\Attributes\Locked;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\ChangelogController;

class Index extends Component
{
    public const PER_PAGE = 20;

    /**
     * The project slug or the without project filter value, already validated by the controller.
     */
    #[Locked]
    public ?string $projectFilter = null;

    /**
     * The page the visitor landed on, further pages are appended while scrolling.
     */
    #[Locked]
    public int $firstPage = 1;

    public int $pagesLoaded = 1;

    protected $listeners = [
        'item-created' => '$refresh',
    ];

    public function loadMore(): void
    {
        $this->pagesLoaded++;
    }

    public function render(): View
    {
        $changelogLimit = $this->pagesLoaded * self::PER_PAGE;
        $isFilteringWithoutProject = $this->projectFilter === ChangelogController::WITHOUT_PROJECT_FILTER;

        $changelogs = Changelog::query()
            ->published()
            ->with(['user', 'project'])
            ->when(app(GeneralSettings::class)->show_changelog_like, fn (Builder $query) => $query
                ->withCount('votes')
                ->withExists(['votes as has_voted' => fn (Builder $query) => $query->where('user_id', auth()->id())]))
            ->when($isFilteringWithoutProject, fn (Builder $query) => $query->whereNull('project_id'))
            ->when($this->projectFilter && !$isFilteringWithoutProject, fn (Builder $query) => $query->whereRelation('project', 'slug', $this->projectFilter))
            ->offset(($this->firstPage - 1) * self::PER_PAGE)
            ->limit($changelogLimit + 1)
            ->get();

        return view('livewire.changelog.index', [
            'changelogs' => $changelogs->take($changelogLimit),
            'hasMoreChangelogs' => $changelogs->count() > $changelogLimit,
            'previousPageUrl' => $this->firstPage > 1 ? $this->pageUrl($this->firstPage - 1) : null,
            'nextPageUrl' => $this->pageUrl($this->firstPage + $this->pagesLoaded),
        ]);
    }

    protected function pageUrl(int $page): string
    {
        return ChangelogController::overviewUrl($this->projectFilter, $page);
    }
}
