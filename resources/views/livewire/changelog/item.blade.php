<div class="bg-white dark:bg-gray-800 rounded-xl p-6 md:p-8 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
    <div class="flex justify-between gap-6 flex-col md:flex-row">
        <div class="flex flex-col flex-1 min-w-0">
            <!-- Header -->
            <div class="flex flex-col space-y-3 mb-6">
                <div class="flex items-start justify-between gap-4">
                    <h1 class="font-bold text-2xl md:text-3xl text-gray-900 dark:text-white leading-tight">
                        {{ $changelog->title }}
                    </h1>

                    @if(!request()->routeIs('changelog.show'))
                        <a href="{{ route('changelog.show', $changelog) }}"
                           class="shrink-0 text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300 transition">
                            View →
                        </a>
                    @endif
                </div>

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
                {!! str($changelog->content)->markdown() !!}
            </div>

        </div>

        @if(app(App\Settings\GeneralSettings::class)->show_changelog_like)
            <div class="md:self-start">
                <livewire:changelog.vote :changelog="$changelog"/>
            </div>
        @endif
    </div>

    @if(app(App\Settings\GeneralSettings::class)->show_changelog_related_items && $changelog->items->count())
        <div class="w-full bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6 mt-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Related Items</h3>
            <div class="space-y-6">
                {{--@foreach($changelog->items()->->get() as $item)
                    <a title="{{ $item->title }}"
                       href="{{ route('items.show', $item) }}"
                        class="w-full flex items-center h-6 px-2 text-sm font-semibold tracking-tight text-primary-800 rounded-md bg-primary-500/5 hover:bg-primary-700/10 shadow hover:scale-[1.015]">
                        <span class="no-underline truncate">{{ $item->title }} {{ $item->project ? '(' . $item->project->title . ')' : '' }}</span>
                    </a>
                @endforeach--}}
                @php
                    $tags = \App\Models\Tag::query()
                        ->forChangelog($changelog)
                        ->get();

                    $items = $changelog->items()
                        ->noChangelogTag()
                        ->get();
                @endphp

                <div>
                    <ul class="space-y-2">
                        @foreach($items as $item)
                            <li class="flex items-start gap-2">
                                <span class="text-brand-500 dark:text-brand-400 shrink-0 leading-none">•</span>
                                <a href="{{ route('items.show', $item) }}"
                                   class="text-gray-700 dark:text-gray-300 hover:text-brand-600 dark:hover:text-brand-400 transition font-medium leading-relaxed">
                                    {{ $item->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @foreach($tags as $tag)
                    <div>
                        <div class="border-b border-gray-300 dark:border-gray-600 pb-2 mb-3">
                            <h4 class="text-sm uppercase font-bold text-gray-700 dark:text-gray-300 tracking-wider">{{ $tag->name }}</h4>
                        </div>

                        <ul class="space-y-2">
                            @foreach($tag->items as $item)
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-500 dark:text-brand-400 shrink-0 leading-none">•</span>
                                    <a href="{{ route('items.show', $item) }}"
                                       class="text-gray-700 dark:text-gray-300 hover:text-brand-600 dark:hover:text-brand-400 transition font-medium leading-relaxed">
                                        {{ $item->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
