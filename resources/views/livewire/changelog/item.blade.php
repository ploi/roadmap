<div class="bg-white dark:bg-gray-800 rounded-xl p-6 md:p-8 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
    <div class="flex flex-col min-w-0">
        <!-- Header -->
        <div class="flex flex-col gap-3 mb-6">
            @if($changelog->project?->isVisibleForCurrentUser())
                <x-project-badge :project="$changelog->project" class="self-start"/>
            @endif

            <h1 class="font-bold text-2xl md:text-3xl text-gray-900 dark:text-white leading-tight">
                {{ $changelog->title }}
            </h1>

            @if(app(App\Settings\GeneralSettings::class)->show_changelog_author)
                <div class="flex items-center gap-3">
                    <div class="relative w-6 h-6 rounded-full ring-2 ring-gray-200 dark:ring-gray-700">
                        <img class="absolute inset-0 object-cover rounded-full"
                             src="{{ $changelog->user->getGravatar() }}"
                             alt="{{ $changelog->user->name }}">
                    </div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $changelog->user->name }} • {{ $changelog->published_at->isoFormat('L') }}
                    </span>
                </div>
            @else
                <time class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                    {{ $changelog->published_at->isoFormat('LL') }}
                </time>
            @endif
        </div>

        <!-- Content -->
        <div class="prose prose-gray dark:prose-invert max-w-none break-words">
            {!! $changelog->content_html !!}
        </div>

        @if(app(App\Settings\GeneralSettings::class)->show_changelog_like)
            <div class="mt-6">
                <livewire:changelog.vote :changelog="$changelog" :show-voters="true"/>
            </div>
        @endif
    </div>

    @if($includedItemGroups->isNotEmpty())
        <section class="mt-8 border-t border-gray-200 pt-6 dark:border-gray-700">
            <h2 class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                {{ trans('changelog.included-items') }}
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-white/5 dark:text-gray-400">
                    {{ $includedItemsCount }}
                </span>
            </h2>

            <div class="mt-4 space-y-5">
                @foreach($includedItemGroups as $group)
                    <div>
                        @if($group['title'])
                            <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ $group['title'] }}
                            </h3>
                        @endif

                        <ul class="divide-y divide-gray-200 overflow-hidden rounded-lg border border-gray-200 dark:divide-gray-700 dark:border-gray-700">
                            @foreach($group['items'] as $item)
                                <li>
                                    <a href="{{ $item->view_url }}"
                                       class="flex items-center gap-3 px-4 py-3 transition hover:bg-gray-50 dark:hover:bg-white/5">
                                        <span class="min-w-0 flex-1 truncate text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $item->title }}
                                        </span>

                                        @if($item->project && ! $item->project->is($changelog->project))
                                            <span class="hidden shrink-0 items-center gap-1.5 text-xs text-gray-500 sm:inline-flex dark:text-gray-400">
                                                <x-project-icon :project="$item->project" class="size-3.5 shrink-0"/>
                                                {{ $item->project->title }}
                                            </span>
                                        @endif

                                        @if($item->board)
                                            <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 dark:bg-white/5 dark:text-gray-300">
                                                {{ $item->board->title }}
                                            </span>
                                        @endif

                                        <span class="inline-flex shrink-0 items-center gap-1 text-xs tabular-nums text-gray-500 dark:text-gray-400"
                                              x-data
                                              x-tooltip.raw="{{ trans_choice('messages.total-votes', $item->total_votes, ['votes' => $item->total_votes]) }}">
                                            <x-heroicon-o-hand-thumb-up class="size-4"/>
                                            {{ $item->total_votes }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
