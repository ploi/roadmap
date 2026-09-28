<div>
    @if($previousPageUrl)
        <div class="mb-6 flex justify-center md:ml-[10.5rem]">
            <a href="{{ $previousPageUrl }}" rel="prev" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                &larr; {{ trans('changelog.newer-changes') }}
            </a>
        </div>
    @endif

    @if($changelogs->isNotEmpty())
        @php($showAuthor = app(App\Settings\GeneralSettings::class)->show_changelog_author)
        @php($showLikes = app(App\Settings\GeneralSettings::class)->show_changelog_like)
        @php($groupByMonth = app(App\Settings\GeneralSettings::class)->group_changelog_by_month)

        <div class="w-full space-y-8">
            @foreach($changelogs->groupBy(fn ($changelog) => $changelog->published_at->format($groupByMonth ? 'Y-m' : 'Y-m-d')) as $date => $changelogsOnDate)
                <section class="md:grid md:grid-cols-[9rem_1fr] md:gap-6">
                    <div class="mb-2 md:mb-0 md:pt-5">
                        <time datetime="{{ $date }}" class="block text-sm font-medium text-gray-500 md:sticky md:top-20 dark:text-gray-400">
                            {{ $changelogsOnDate->first()->published_at->isoFormat($groupByMonth ? 'MMMM YYYY' : 'LL') }}
                        </time>
                    </div>

                    <ul class="divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white shadow-sm dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800">
                        @foreach($changelogsOnDate as $changelog)
                            @php($project = $changelog->project?->isVisibleForCurrentUser() ? $changelog->project : null)

                            <li class="flex flex-col gap-2 p-5">
                                @if($project || $showAuthor || $groupByMonth)
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                        @if($project)
                                            <x-project-badge :project="$project"/>
                                        @endif

                                        @if($showAuthor)
                                            <span class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                                <img class="size-4 rounded-full object-cover"
                                                     src="{{ $changelog->user->getGravatar() }}"
                                                     alt="">
                                                {{ $changelog->user->name }}
                                            </span>
                                        @endif

                                        @if($groupByMonth)
                                            <time datetime="{{ $changelog->published_at->toDateString() }}" class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $changelog->published_at->isoFormat('ll') }}
                                            </time>
                                        @endif
                                    </div>
                                @endif

                                <h2 class="text-lg font-semibold leading-snug text-gray-900 dark:text-white">
                                    <a href="{{ route('changelog.show', $changelog) }}"
                                       class="transition hover:text-brand-600 dark:hover:text-brand-400">
                                        {{ $changelog->title }}
                                    </a>
                                </h2>

                                <div class="prose prose-sm prose-gray max-w-none break-words dark:prose-invert prose-p:my-0">
                                    {!! $changelog->excerpt_html !!}
                                </div>

                                @if($showLikes || $changelog->hasMoreContentThanExcerpt())
                                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                                        @if($showLikes)
                                            <livewire:changelog.vote :changelog="$changelog" :key="'changelog-vote-' . $changelog->id"/>
                                        @endif

                                        @if($changelog->hasMoreContentThanExcerpt())
                                            <a href="{{ route('changelog.show', $changelog) }}"
                                               class="text-sm font-medium text-brand-600 transition hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                                {{ trans('changelog.read-more') }} &rarr;
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>

        @if($hasMoreChangelogs)
            <div wire:key="load-more-{{ $pagesLoaded }}"
                 x-intersect.margin.400px="$wire.loadMore()"
                 class="mt-8 flex justify-center md:ml-[10.5rem]">
                <a href="{{ $nextPageUrl }}"
                   rel="next"
                   wire:click.prevent="loadMore"
                   wire:loading.remove
                   wire:target="loadMore"
                   class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    {{ trans('changelog.older-changes') }} &rarr;
                </a>

                <span wire:loading wire:target="loadMore" class="py-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ trans('changelog.loading') }}
                </span>
            </div>
        @endif
    @else
        <div class="w-full">
            <div class="flex flex-col items-center justify-center max-w-md p-8 mx-auto space-y-6 text-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
                <div class="flex items-center justify-center w-20 h-20 text-brand-500 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/20 rounded-full">
                    <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1.5"
                              d="M5.75 12.8665L8.33995 16.4138C9.15171 17.5256 10.8179 17.504 11.6006 16.3715L18.25 6.75"/>
                    </svg>
                </div>

                <header class="max-w-sm space-y-2">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ trans('changelog.all-caught-up-title') }}</h2>

                    <p class="text-base text-gray-600 dark:text-gray-400">
                        {{ trans('changelog.all-caught-up-description') }}
                    </p>
                </header>
            </div>
        </div>
    @endif
</div>
