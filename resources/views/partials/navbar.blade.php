<div class="hidden lg:block">
    <aside class="w-60" aria-label="Sidebar">
        <div class="overflow-y-auto space-y-4">
            <ul class="space-y-2 pb-4 border-b border-gray-200 dark:border-white/10">
                <li>
                    <a
                        @class([
                                'flex items-center h-10 px-2 space-x-2 transition rounded-lg',
                                'text-white bg-brand-500 dark:bg-white/5 dark:hover:bg-white/5 dark:text-brand-400' => request()->is('/'),
                                'hover:bg-gray-500/5 focus:bg-brand-500/10 focus:text-brand-600 focus:outline-none dark:hover:bg-white/5 dark:text-brand-500' => !request()->is('/')
                            ])
                        href="{{ route('home') }}">

                        <x-heroicon-o-home @class([
                            'w-5 h-5',
                            'text-gray-500' => !request()->is('/')
                        ])/>

                        <span @class([
                            'font-normal',
                            'text-gray-900 dark:text-gray-200' => !request()->is('/')
                        ])>{{ trans('general.dashboard') }}</span>
                    </a>
                </li>

                <li>
                    <a
                        @class([
                            'flex items-center h-10 px-2 space-x-2 transition rounded-lg',
                            'text-white bg-brand-500 dark:bg-white/5 dark:hover:bg-white/5 dark:text-brand-400' => request()->is('my'),
                            'hover:bg-gray-500/5 focus:bg-brand-500/10 focus:text-brand-600 focus:outline-none dark:hover:bg-white/5 dark:focus:text-gray-200 dark:text-gray-200' => !request()->is('my')
                        ])
                        href="{{ route('my') }}">
                        <x-heroicon-o-queue-list @class([
                            'w-5 h-5',
                            'text-gray-500' => !request()->is('my')
                        ])/>

                        <span class="font-medium">{{ trans('items.my-items') }}</span>
                    </a>
                </li>

                <li>
                    <a
                        @class([
                            'flex items-center h-10 px-2 space-x-2 transition rounded-lg',
                            'text-white bg-brand-500 dark:bg-white/5 dark:hover:bg-white/5 dark:text-brand-400' => request()->is('profile'),
                            'hover:bg-gray-500/5 focus:bg-brand-500/10 focus:text-brand-600 focus:outline-none dark:hover:bg-white/5 dark:focus:text-gray-200 dark:text-gray-200' => !request()->is('profile')
                        ])
                        href="{{ route('profile') }}">
                        <x-heroicon-o-user @class([
                            'w-5 h-5',
                            'text-gray-500' => !request()->is('profile')
                        ])/>

                        <span class="font-medium">{{ trans('auth.profile') }}</span>
                    </a>
                </li>

                <li>
                    <a
                        @class([
                            'flex items-center h-10 px-2 space-x-2 transition rounded-lg',
                            'text-white bg-brand-500 dark:bg-white/5 dark:hover:bg-white/5 dark:text-brand-400' => request()->is('activity'),
                            'hover:bg-gray-500/5 focus:bg-brand-500/10 focus:text-brand-600 focus:outline-none dark:hover:bg-white/5 dark:focus:text-gray-200 dark:text-gray-200' => !request()->is('activity')
                        ])
                        href="{{ route('activity') }}">
                        <x-heroicon-o-clock @class([
                            'w-5 h-5',
                            'text-gray-500' => !request()->is('activity')
                        ])/>

                        <span class="font-medium">{{ trans('general.activity') }}</span>
                    </a>
                </li>

                @if(app(App\Settings\GeneralSettings::class)->enable_changelog)
                    <li>
                        <a
                            @class([
                                'flex items-center h-10 px-2 space-x-2 transition rounded-lg',
                                'text-white bg-brand-500 dark:bg-white/5 dark:hover:bg-white/5 dark:text-brand-400' => request()->is('changelog*'),
                                'hover:bg-gray-500/5 focus:bg-brand-500/10 focus:text-brand-600 focus:outline-none dark:hover:bg-white/5 dark:focus:text-gray-200 dark:text-gray-200' => !request()->is('changelog*')
                            ])
                            href="{{ route('changelog') }}">
                            <x-heroicon-o-rss @class([
                                'w-5 h-5',
                                'text-gray-500' => !request()->is('changelog*')
                            ])/>

                            <span class="font-medium">{{ trans('changelog.changelog') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
            <div>
                {{--
                <p class="px-2 text-lg font-semibold mb-2">{{ trans('projects.projects') }}</p>
                --}}
                @if($projects->count() > 0)
                    <ul class="space-y-2">
                        @foreach($projects->groupBy('group') as $group => $groupProjects)
                            @if($group)
                                <li class="mb-3">
                                <div class="flex items-center h-2 px-2 space-x-2 transition rounded-lg mt-5">
                                    <span class="font-normal text-gray-500 truncate">{{ $group }}</span>
                                </div>
                                </li>
                            @endif

                            @php
                                $nonCollapsible = $groupProjects->where('collapsible', false);
                                $collapsible = $groupProjects->where('collapsible', true);
                                $groupId = 'dropdown-projects-' . ($group ? Str::slug($group) : 'default');
                            @endphp

                            {{-- Non-collapsible projects - always visible --}}
                            @foreach($nonCollapsible as $project)
                                <li>
                                    <a
                                        title="{{ $project->title }}"
                                        @class([
                                       'flex items-center h-10 px-2 space-x-2 transition rounded-lg',
                                       'text-white bg-brand-500 dark:bg-white/5 dark:hover:bg-white/5 dark:text-brand-400' => request()->segment(2) === $project->slug,
                                       'hover:bg-gray-500/5 focus:bg-brand-500/10 focus:text-brand-600 focus:outline-none dark:hover:bg-white/5 dark:focus:text-gray-200 dark:text-gray-200' => request()->segment(2) !== $project->slug
                                   ])
                                        href="{{ route('projects.show', $project) }}">
                                        <x-dynamic-component :component="$project->icon ?? 'heroicon-o-hashtag'" @class([
                                            'shrink-0 w-5 h-5',
                                            'text-gray-500' => request()->segment(2) != $project->slug
                                        ])/>

                                        <span class="font-normal truncate">{{ $project->title }}</span>

                                        @if($project->private)
                                            <div class="flex-1 flex justify-end">
                                                <x-heroicon-s-lock-closed @class([
                                                    'w-4 h-4',
                                                    'text-primary' => request()->segment(2) != $project->slug
                                                ])/>
                                            </div>
                                        @endif
                                    </a>
                                </li>
                            @endforeach

                            {{-- Collapsible projects - hidden behind dropdown --}}
                            @if($collapsible->count() > 0)
                                <div x-data="{ open: {{ Request::is('projects/*') ? 'true' : 'false' }} }">
                                    <button type="button"
                                        @click="open = !open"
                                        class="flex items-center w-full p-2 text-base font-normal text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                        aria-controls="{{ $groupId }}"
                                        data-collapse-toggle="{{ $groupId }}">
                                        <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>
                                        {{ trans('projects.projects') }}</span>
                                        <svg
                                            sidebar-toggle-item
                                            class="w-6 h-6 transition-transform duration-200"
                                            :class="{ 'rotate-180': open }"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    <ul id="{{ $groupId }}" x-show="open" x-transition class="py-2 space-y-2">
                                    @foreach($collapsible as $project)
                                        <li>
                                            <a
                                                title="{{ $project->title }}"
                                                @class([
                                               'flex items-center h-10 px-2 space-x-2 transition rounded-lg',
                                               'text-white bg-brand-500 dark:bg-white/5 dark:hover:bg-white/5 dark:text-brand-400' => request()->segment(2) === $project->slug,
                                               'hover:bg-gray-500/5 focus:bg-brand-500/10 focus:text-brand-600 focus:outline-none dark:hover:bg-white/5 dark:focus:text-gray-200 dark:text-gray-200' => request()->segment(2) !== $project->slug
                                           ])
                                                href="{{ route('projects.show', $project) }}">
                                                <x-dynamic-component :component="$project->icon ?? 'heroicon-o-hashtag'" @class([
                                                    'shrink-0 w-5 h-5',
                                                    'text-gray-500' => request()->segment(2) != $project->slug
                                                ])/>

                                                <span class="font-normal truncate">{{ $project->title }}</span>

                                                @if($project->private)
                                                    <div class="flex-1 flex justify-end">
                                                        <x-heroicon-s-lock-closed @class([
                                                            'w-4 h-4',
                                                            'text-primary' => request()->segment(2) != $project->slug
                                                        ])/>
                                                    </div>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <div class="px-2">
                        <span class="text-sm text-gray-500">{{ trans('projects.no-projects') }}</span>
                    </div>
                @endif
            </div>

            <div id="dropdown-cta" class="p-4 mt-6 bg-gray-100 rounded-lg dark:bg-white/5" role="alert">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <a href="https://github.com/ploi/roadmap" target="_blank"
                       class="font-semibold border-b border-dotted">Open-source</a>
                    roadmapping software by
                    <a href="https://ploi.io/?ref=roadmap" target="_blank" class="font-semibold border-b border-dotted">ploi.io</a>
                </p>

                <p class="text-[0.6rem] text-gray-400">
                    Running version {{ (new \App\Services\SystemChecker())->getApplicationVersion() }}
                </p>
            </div>
        </div>
    </aside>
</div>
