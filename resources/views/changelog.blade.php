@use('App\Http\Controllers\ChangelogController')

@php
    $isShowingChangelog = isset($changelog);
    $isFilteringWithoutProject = !$isShowingChangelog && $projectFilter === ChangelogController::WITHOUT_PROJECT_FILTER;

    $breadcrumbs = collect([
        ['title' => trans('resources.changelog.label'), 'url' => route('changelog')],
    ])
        ->when($selectedProject, fn ($collection) => $collection->push(['title' => $selectedProject->title, 'url' => ChangelogController::overviewUrl($selectedProject->slug)]))
        ->when($isFilteringWithoutProject, fn ($collection) => $collection->push(['title' => trans('changelog.filter.without-project'), 'url' => ChangelogController::overviewUrl(ChangelogController::WITHOUT_PROJECT_FILTER)]))
        ->when($isShowingChangelog, fn ($collection) => $collection->push(['title' => $changelog->title, 'url' => route('changelog.show', $changelog)]));
@endphp

@section('title', 'Changelog')@show
@section('image', App\Services\OgImageGenerator::make('View changelog')->withSubject('Changelog')->withFilename('changelog.jpg')->generate()->getPublicUrl())
@section('description', 'View changelog for ' . config('app.name'))
@section('canonical', $isShowingChangelog ? route('changelog.show', $changelog) : ChangelogController::overviewUrl($projectFilter, $page))@show

<x-app :breadcrumbs="$breadcrumbs->toArray()">
    <div class="max-w-5xl">
        @if($isShowingChangelog)
            <livewire:changelog.item :changelog="$changelog"/>
        @else
            @if($filterProjects->isNotEmpty())
                @php
                    $filterClasses = 'inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-sm font-medium transition';
                    $activeFilterClasses = 'border-transparent bg-brand-500 text-white dark:bg-white/10 dark:text-white';
                    $inactiveFilterClasses = 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700';
                @endphp

                <nav class="mb-6 flex flex-wrap gap-2 md:ml-[10.5rem]">
                    <a href="{{ route('changelog') }}"
                       @if(blank($projectFilter)) aria-current="page" @endif
                       @class([$filterClasses, blank($projectFilter) ? $activeFilterClasses : $inactiveFilterClasses])>
                        {{ trans('changelog.filter.all') }}
                    </a>

                    @if($hasChangelogsWithoutProject)
                        <a href="{{ ChangelogController::overviewUrl(ChangelogController::WITHOUT_PROJECT_FILTER) }}"
                           @if($isFilteringWithoutProject) aria-current="page" @endif
                           @class([$filterClasses, $isFilteringWithoutProject ? $activeFilterClasses : $inactiveFilterClasses])>
                            {{ trans('changelog.filter.without-project') }}
                        </a>
                    @endif

                    @foreach($filterProjects as $project)
                        <a href="{{ ChangelogController::overviewUrl($project->slug) }}"
                           @if($selectedProject?->is($project)) aria-current="page" @endif
                           @class([$filterClasses, $selectedProject?->is($project) ? $activeFilterClasses : $inactiveFilterClasses])>
                            <x-project-icon :project="$project" class="size-4 shrink-0"/>
                            {{ $project->title }}
                        </a>
                    @endforeach
                </nav>
            @endif

            <livewire:changelog.index :project-filter="$projectFilter" :first-page="$page"/>
        @endif
    </div>
</x-app>
