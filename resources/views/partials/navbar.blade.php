<div class="hidden lg:block">
    <aside class="w-60" aria-label="Sidebar">
        <div class="overflow-y-auto space-y-4">
            <ul class="space-y-2">
                <li>
                    <a
                        @class([
                                'flex items-center h-10 px-2 space-x-2 transition rounded-lg',
                                'text-white bg-brand-500 dark:bg-white/5 dark:hover:bg-white/5 dark:text-brand-400' => request()->is('/'),
                                'hover:bg-gray-500/5 focus:bg-brand-500/10 focus:text-brand-600 focus:outline-none dark:hover:bg-white/5 dark:text-brand-500' => !request()->is('/')
                            ])
                        href="{{ route('home') }}">

                        <x-heroicon-o-home class="w-5 h-5 {{ !request()->is('/') ? 'text-gray-500' : ''  }}"/>

                        <span
                            class="font-normal {{ !request()->is('/') ? 'text-gray-900 dark:text-gray-200' : ''  }}">{{ trans('general.dashboard') }}</span>
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
                        <x-heroicon-o-queue-list class="w-5 h-5 {{ !request()->is('my') ? 'text-gray-500' : ''  }}"/>

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
                        <x-heroicon-o-user class="w-5 h-5 {{ !request()->is('profile') ? 'text-gray-500' : ''  }}"/>

                        <span class="font-medium">{{ trans('auth.profile') }}</span>
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
                            <x-heroicon-o-rss
                                class="w-5 h-5 {{ !request()->is('changelog*') ? 'text-gray-500' : ''  }}"/>

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
                        @foreach($projects->groupBy('group') as $group => $projects)
                            @if($group)
                                <li class="mb-3">
                                <div
                                    class="flex items-center h-2 px-2 space-x-2 transition rounded-lg mt-5"
                                >
                                    <span class="font-normal text-gray-500 truncate">{{ $group }}</span>

                                </div>
                                </li>
                            @endif
                    <button type="button" class="flex items-center w-full p-2 text-base font-normal text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700" aria-controls="dropdown-projects" data-collapse-toggle="dropdown-projects">
                  
                  <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>
                    {{ trans('projects.projects') }}
                  </span>
                  <svg sidebar-toggle-item class="w-6 h-6" fill="#013852" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
            <ul id="dropdown-projects" class="hidden py-2 space-y-2">
                            @foreach($projects as $project)
                                <li>
                                    <a
                                        title="{{ $project->title }}"
                                        @class([
                                       'flex items-center h-10 px-2 space-x-2 transition rounded-lg',
                                       'text-white bg-brand-500 dark:bg-white/5 dark:hover:bg-white/5 dark:text-brand-400' => request()->segment(2) === $project->slug,
                                       'hover:bg-gray-500/5 focus:bg-brand-500/10 focus:text-brand-600 focus:outline-none dark:hover:bg-white/5 dark:focus:text-gray-200 dark:text-gray-200' => request()->segment(2) !== $project->slug
                                   ])
                                        href="{{ route('projects.show', $project) }}">
                                        <x-dynamic-component :component="$project->icon ?? 'heroicon-o-hashtag'"
                                                             class="flex-shrink-0 w-5 h-5 {{ request()->segment(2) == $project->slug ? '' : 'text-gray-500'  }}"/>

                                        <span class="font-normal truncate">{{ $project->title }}</span>

                                        @if($project->private)
                                            <div class="flex-1 flex justify-end">
                                                <x-heroicon-s-lock-closed
                                                    class="w-4 h-4 {{ request()->segment(2) == $project->slug ? '' : 'text-primary'  }}"/>
                                            </div>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
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
